# Criminal Case Workflow — Technical Analysis

Scope: the "Criminal Justice" module under `App\Http\Controllers\Oag\Crime`, `App\Models\Oag\Crime`, table prefix `cases` (migration `2024_08_26_032105_create_criminal_justice_system_tables.php`). Civil case module is out of scope.

## 1. Core entities and how they relate

```
cases (CriminalCase)                 "the case file"
 ├─ belongsTo lawyer (users.lawyer_id)
 ├─ belongsTo island (islands.island_id)
 ├─ belongsTo closureReason (reasons_for_closure.reason_for_closure_id)   ⚠ see §5.1
 ├─ hasMany accused (accused.case_id)
 ├─ belongsToMany offences (case_offence pivot: offence_particulars, category_id)
 │
 ├─ hasMany victims          (victims.case_id)            — no model relation defined on CriminalCase
 ├─ hasMany incidents        (incidents.case_id)           — no model relation defined on CriminalCase
 ├─ hasOne  case_reviews     (case_reviews.case_id, UNIQUE) — "one review per case" (see §5.2)
 ├─ hasMany case_reallocations (case_reallocations.case_id)
 ├─ hasMany court_cases      (court_cases.case_id)          — High Court stage
 ├─ hasMany appeal_details   (appeal_details.case_id)       — Appeal stage (generic)
 └─ hasMany court_of_appeals (court_of_appeals.case_id)     — Court of Appeal stage
```

Supporting lookup tables: `islands`, `reasons_for_closure`, `offence_categories`, `offences`, `courts` (Court of Appeal venues), `users` (lawyers/reviewers, via Spatie roles `cm.user` / `cm.admin`).

`accused_offence` pivot exists in the migration but the `Accused` model workflow currently syncs offences via the **case**-level `offences()` relation (`case_offence` pivot) during Case Review, and the `AccusedController::update` also calls `$accused->offences()->sync(...)`, implying `Accused` also has its own `offences()` relation to `accused_offence`. Two parallel offence-linking mechanisms exist — worth confirming which one the UI/reports actually read from (see §5.3).

## 2. The case status machine

`cases.status` is an enum:
`pending | accepted | rejected | reallocated | allocated | closed | reviewed | appealed | courtcased`
(default `pending`).

Status is **not** driven by a formal state machine class — it's set ad-hoc from several controllers. Observed transitions:

| From | Action (controller method) | To | Who |
|---|---|---|---|
| *(new)* | `CriminalCaseController::store` | `pending` | any authenticated user (case creator) |
| `pending` | `CriminalCaseController::accept` | `accepted` | `cm.user` |
| `pending`/any | `CriminalCaseController::reject` (`rejection_reason` required) | `rejected` | `cm.user` |
| `pending` | `CriminalCaseController::allocateLawyer` (first allocation, `lawyer_id` was null) | `allocated` | `cm.admin` (form gated to `status === 'pending'` in UI) |
| `rejected` (UI-gated) | `CriminalCaseController::allocateLawyer` (`lawyer_id` already set → treated as reallocation) | `allocated` (⚠ should arguably be `reallocated`, see §5.4) | `cm.admin` |
| any | `CriminalCaseController::reallocateCase` (dedicated reallocate endpoint) | `reallocated` | `cm.admin` (`hasRole('cm.admin')`) |
| any | `CriminalCaseController::update` (edit form) — **implicit** side effect: if `lawyer_id` changed | `reallocate` (⚠ typo — not a valid enum value, see §5.5) | whoever can edit |
| `accepted` | `CaseReviewController::store`, evidence_status ∈ {`insufficient_evidence`,`returned_to_police`} | `closed` | `cm.user` (Case Review action gated to `status === 'accepted'`) |
| `accepted` | `CaseReviewController::store`, evidence_status = `sufficient_evidence` | `accepted` (stays) + offences synced to case | `cm.user` |
| — | `AppealDetailController::store` | *(no status change — commented out)* | — |
| — | `CourtOfAppealController::store` | *(no status change)* | — |
| — | `CourtCaseController::store` | *(no status change)* | — |

Two enum values (`reviewed`, `appealed`, `courtcased`) exist in the DB schema but **no controller code sets them** — dead states, likely intended for when a Case Review / Court Case / Appeal is filed but never wired up.

## 3. End-to-end happy-path workflow

1. **Create case** — `GET/POST crime/criminalCase` (`CriminalCaseController::create/store`).
   Required: `case_file_number` (unique), `date_file_received`, `case_name`, `island_id`. Optional: `date_of_incident`, `lawyer_id`, `court_case_number`, `reason_for_closure_id`.
   → status `pending`, `created_by` = current user.
   → **redirects straight to** "Add Accused" (`createAccused`), not to the index — the case is unusable as a case file until at least one accused exists.

2. **Add Accused** — `GET crime/criminalCase/{id}/createAccused` → `AccusedController::create/store`.
   Buttons on the form drive redirect chaining:
   - "Save & add victim" → `createVictim`
   - "Save & add another accused" → back to `createAccused`
   - otherwise → accused index.
   Offences can optionally be attached to the accused here too (`offencesByCategory` passed to view) — see §5.3 on the two offence pathways.

3. **Add Victim** (optional, chained from Accused) — `VictimController::createForCase` / `store`.

4. **Add Incident** (optional, separate entry point) — `CriminalCaseController::createIncident` → `IncidentController::createForCase` / `store`. Records place/date range of the incident. Not chained automatically from case creation — reached via "Related Records" or direct link.

5. **Case intake decision** — on the Criminal Cases index (`oag.crime.index`), a `cm.user` sees Accept/Reject buttons on any case not yet `accepted/rejected/closed`:
   - **Accept** → `POST criminalCase/{id}/accept` → status `accepted`, clears `rejection_reason`.
   - **Reject** → `POST criminalCase/{id}/reject` (modal, requires `rejection_reason`) → status `rejected`.

6. **Lawyer allocation** — `cm.admin` only, gated in UI to `status === 'pending'`:
   `GET/POST crime/criminal-case/{id}/allocate` → `CriminalCaseController::showAllocationForm` / `allocateLawyer`.
   - If case had no lawyer yet: records nothing in `case_reallocations`, sets `lawyer_id`, status → `allocated`.
   - If case already had a lawyer (edge case reachable outside the UI gate): also creates a `case_reallocations` row (`CaseReallocationRepository`), still sets status → `allocated` (not `reallocated` — inconsistent with the dedicated reallocate flow, see §5.4).

7. **Case Review** — only offered (per UI) once `status === 'accepted'`:
   `GET crime/CaseReview/{id}/create` → `POST crime/CaseReview/store`.
   - `evidence_status = sufficient_evidence` → attach `offence_id[]` / `category_id[]` + `offence_particulars`, synced onto `case.offences()`; case status stays/moves to `accepted`.
   - `evidence_status ∈ {insufficient_evidence, returned_to_police}` → requires `reason_for_closure_id`; sets `case.date_file_closed = today`, case status → `closed`.
   - `case_reviews` has a **unique constraint on `case_id`** — the repository's `getById($id)` in `CaseReviewController::edit` is actually being passed the **case ID**, not a case_review ID, which happens to work only because of that 1-per-case uniqueness assumption (see §5.2 — fragile).

8. **Court Case (High Court stage)** — offered once `status === 'accepted'`:
   `GET crime/CourtCase/{id}/create` → `CourtCaseController::store`. Records `charge_file_dated`, `high_court_case_number`, `verdict`, `court_outcome` (`win`/`lose`), judgment date. **Does not change `cases.status`.**

9. **Appeal (`appeal_details`)** — always visible to `cm.user` in the Related Records / Actions menu (not status-gated in the JS, unlike Case Review/Court Case):
   `GET crime/appeal/create/{id?}` → `AppealDetailController::create/store`.
   - If launched with a case id, blocks appeal creation when `originalCase->is_appeal_case` or `is_on_appeal` — **but those columns don't exist in the `cases` migration**, so this check will always throw/`null`-fail today (see §5.6).
   - Captures `filing_date_type` (`court` vs `defendant`) → stored in either `appeal_filing_date` or a non-existent `appeal_filing_received_date` column (see §5.7), plus `verdict`, `court_outcome`, `decision_principle_established`.
   - Does **not** update `cases.status` (explicitly commented out).

10. **Court of Appeal (`court_of_appeals`)** — final tier, separate from step 9's `appeal_details` table:
    `GET crime/courtOfAppeal/create/{caseId?}` → `CourtOfAppealController::store`.
    Same shape as Court Case/Appeal (filing date, source, judgment date, outcome — but outcome enum adds `remand`). Does not touch `cases.status`.

11. **Reallocation** (can happen at any point) — two competing implementations, see §5.4:
    - `CriminalCaseController::reallocateCase` (dedicated form, `cm.admin`) — logs to `case_reallocations`, sets status `reallocated`.
    - `CriminalCaseController::update` (plain case edit) — if `lawyer_id` differs from current, silently sets status to the **literal string `'reallocate'`** (typo, not `'reallocated'`, and not a valid DB enum value → will throw a SQL error on save in strict mode, or silently truncate/fail depending on MySQL sql_mode).

## 4. Access control summary (Spatie roles referenced in code)

| Role | Capabilities observed |
|---|---|
| `cm.user` | Accept/Reject a case; create Case Review; create Court Case; create Appeal / Court of Appeal (menu items shown unconditionally for this role) |
| `cm.admin` | Allocate lawyer (first allocation); Reallocate case (both the dedicated endpoint and the "allocate again" path) |
| *(any authenticated)* | Create case, edit case, add accused/victim/incident, delete case |

No role check is present on: `store` (create case), `update` (edit case, which can silently trigger the `'reallocate'` bug), `destroy`, `createAccused/Victim/Incident`. Role gating exists only in the **Blade/JS layer** (`userRoles.can*` flags control which buttons render) — the underlying routes (`allocateForm`, `reallocateCase`, `accept`, `reject`) do enforce `hasRole(...)` server-side via `abort(403)`, but appeal/court-case/case-review store endpoints do **not** re-check the role server-side (only the link is hidden client-side). A user who knows the URL could POST to `CaseReview.store`, `CourtCase.store`, `appeal.store`, `courtOfAppeal.store` regardless of role.

## 5. Issues / inconsistencies worth resolving before continuing

These are things I noticed while tracing the flow — flagging them so you can decide whether they're already known/tracked or need fixing.

1. **`closureReason()` relation is broken.** `CriminalCase::closureReason()` points to `App\Models\Oag\Crime\ClosureReason`, but the table/model actually created and used everywhere else is `ReasonsForClosure` (`app/Models/Oag/Crime/ReasonsForClosure.php`, table `reasons_for_closure`). `ClosureReason::class` does not appear to exist — calling `$criminalCase->closureReason` will throw a class-not-found error.

2. **`CaseReviewController::edit($id)` conflates case ID and case-review ID.** It calls `$this->criminalCaseRepository->getById($id)` for the case AND `$this->caseReviewRepository->getById($id)` for the review, using the *same* `$id`. This "works" only because the route is `crime/CaseReview/{id}` where `{id}` is actually being passed as the **case's** id from the index link generation, relying on the 1-review-per-case unique constraint and hoping the review's own primary key never diverges from the case id. Fragile — if a case is ever deleted/recreated or IDs diverge, this will fetch the wrong (or no) review.

3. **Two offence-linking pathways.** `case_offence` (case ↔ offence, used by `CaseReview::store`) and `accused_offence` (accused ↔ offence, used by `AccusedController::update` via `$accused->offences()`) both exist. Worth confirming (a) `Accused` model actually declares an `offences()` relation to `accused_offence` (not shown in the files reviewed — only `CriminalCase::offences()` was found), and (b) which one your reports (`ReportController`, `reports` table's raw SQL) actually query — a mismatch here would silently drop data from reports.

4. **Reallocation via "allocate again" doesn't set `status = 'reallocated'`.** `allocateLawyer()` always sets `status = 'allocated'` even when it detects `$isReallocation = true` and writes a `case_reallocations` row. Meanwhile the *dedicated* `reallocateCase()` endpoint sets `status = 'reallocated'`. Two paths writing to the same table/relationship with different resulting statuses — the index badge/action logic (`row.status === 'rejected'` gates the "Case Reallocate" menu item) will therefore behave inconsistently depending on which path was used last.

5. **`CriminalCaseController::update()` sets an invalid status string.** When `lawyer_id` changes via the plain edit form, `$data['status'] = 'reallocate'` (missing final "d"). This is not one of the `cases.status` enum values (`pending, accepted, rejected, reallocated, allocated, closed, reviewed, appealed, courtcased`), so the `UPDATE` will fail under strict SQL mode, or (if MySQL is non-strict) get coerced to the enum's default/empty value, silently corrupting the status. This is a good candidate to fix first since it's a one-line, unambiguous typo.

6. **`AppealDetailController::create()` references non-existent columns.** `$originalCase->is_appeal_case` / `$originalCase->is_on_appeal` are read to block duplicate appeals, and `CriminalCaseRepository::getNonAppealCases()` has the equivalent `where()` clauses **commented out** for the same reason — these columns were never added to the `cases` migration. Today, `create($id)` will treat `is_appeal_case`/`is_on_appeal` as null/falsy (no crash, since Eloquent returns null for unknown attributes rather than throwing, but the guard is a no-op), and `getNonAppealCases()` returns *all* cases, not just non-appeal ones. If the intent is "don't let a case be appealed twice," that guard doesn't currently work.

7. **`AppealDetailController::store()` writes to a non-existent column on the "defendant" filing-date path.** When `filing_date_type === 'defendant'`, the code sets `$data['appeal_filing_received_date']`, but the migration only defines `appeal_filing_date` on `appeal_details` (no `appeal_filing_received_date`). This branch will throw an "Unknown column" SQL error the first time a user picks "defendant" as the filing date source.

8. **`AppealDetailController::update()` validates fields that don't exist on the table** (`appeal_status`, `appeal_grounds`, `appeal_decision`, `appeal_decision_date` are not columns on `appeal_details` per the migration — the table has `verdict`, `court_outcome`, `judgment_delivered_date`, `decision_principle_established` instead). Editing an existing appeal will likely fail validation-to-save mismatch or silently drop unknown attributes depending on `$fillable` on the `AppealDetail` model.

9. **Route name drift.** Several controllers `redirect()->route(...)` to names that don't match `routes/web.php`'s actual registered names, e.g. `AppealDetailController::update` → `crime.appeals.index` (route is `crime.appeal.*` singular, and there's no separate `index` route name distinct from the resource's `appeal.index`), `CaseReviewController::update` → `crime.case_reviews.index` (registered resource name is `crime.CaseReview.*`, capital C). These will throw `RouteNotFoundException` when hit.

10. **No server-side role enforcement on `CaseReview::store`, `CourtCase::store`, `AppealDetail::store`, `CourtOfAppeal::store`.** Only the UI hides the links from non-`cm.user` accounts; the routes themselves don't call `hasRole()`/`abort(403)` the way `accept`, `reject`, `allocateLawyer`, `reallocateCase` do.

## 6. Suggested order of attack

If you want to stabilize this module before adding new features, the highest-value/lowest-risk fixes are (in order):
1. Fix the `'reallocate'` → `'reallocated'` typo in `CriminalCaseController::update()` (§5.5) — one line, prevents silent data corruption.
2. Fix `closureReason()` to point at `ReasonsForClosure::class` (§5.1) — one line, currently guaranteed to throw if ever called.
3. Reconcile the two "allocate again" vs "reallocate" flows so both set `status = 'reallocated'` consistently (§5.4).
4. Fix the route-name typos causing `RouteNotFoundException` on appeal/case-review update (§5.9).
5. Decide whether `is_appeal_case`/`is_on_appeal` should be real columns (migration + backfill) or whether that guard should be removed (§5.6).
6. Fix `appeal_filing_received_date` (§5.7) and the `appeal_status`/`appeal_grounds` validation mismatch (§5.8) — these need a decision on whether to add columns or simplify the form.
7. Add server-side role checks to the four unguarded store endpoints (§5.10).
