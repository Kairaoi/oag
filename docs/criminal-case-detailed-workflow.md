# Criminal Case — Detailed Workflow

This is the process-level companion to [`criminal-case-workflow.md`](criminal-case-workflow.md) (which covers entities, the status state machine, and known bugs). This document walks the full lifecycle of a criminal case stage by stage: who does it, which route/controller/view is involved, exactly what fields are captured, what validation applies, and what happens to the record afterward.

Roles referenced (Spatie): **`cm.user`** (case officer / reviewer) and **`cm.admin`** (allocations lead). Anything not marked with a role is reachable by any authenticated user.

---

## 0. Status lifecycle at a glance

```
                                   ┌─────────────┐
                                   │   pending   │  (created)
                                   └──────┬──────┘
                      cm.user             │             cm.admin
                 accept/reject ───────────┼─────────── allocate lawyer
                                          │
              ┌───────────────┬──────────┴─────────┐
              ▼               ▼                    ▼
        ┌───────────┐  ┌────────────┐       ┌────────────┐
        │  rejected │  │  accepted  │       │  allocated │
        └─────┬─────┘  └─────┬──────┘       └─────┬──────┘
              │              │                     │
     cm.admin │              │ Case Review    cm.admin reallocates
   "allocate  │              │ (evidence)          │
    again"    │        ┌─────┴──────┐              ▼
              │        ▼            ▼        ┌──────────────┐
              │  ┌───────────┐ ┌──────────┐   │  reallocated │
              └─▶│  closed   │ │ accepted │◀──┴──────────────┘
                 │(insuffic. │ │ (stays — │
                 │ evidence/ │ │sufficient│
                 │ returned  │ │evidence, │
                 │to police) │ │offences  │
                 └───────────┘ │ attached)│
                                └────┬─────┘
                                     │  (parallel, no status change)
                          ┌──────────┼──────────────┐
                          ▼          ▼              ▼
                    Court Case   Appeal      Court of Appeal
                   (High Court) (appeal_     (court_of_appeals)
                                 details)
```

Enum values that exist on the `cases.status` column but are **never written by any controller** today: `reviewed`, `appealed`, `courtcased`. Treat these as reserved/aspirational, not live states.

---

## Stage 1 — Case intake (create the case file)

- **Route:** `GET crime/criminalCase/create`, `POST crime/criminalCase` (named `crime.criminalCase.create` / `crime.criminalCase.store`)
- **Controller:** `CriminalCaseController::create` / `store`
- **View:** `oag.crime.create`
- **Actor:** any authenticated user
- **Fields captured:**
  | Field | Rule |
  |---|---|
  | `case_file_number` | required, string, max 255, **unique** across `cases` |
  | `date_file_received` | required, date |
  | `case_name` | required, string, max 255 |
  | `date_of_incident` | nullable, date |
  | `date_file_closed` | nullable, date |
  | `reason_for_closure_id` | nullable, must exist in `reasons_for_closure` |
  | `lawyer_id` | nullable, must exist in `users` |
  | `island_id` | required, must exist in `islands` |
  | `court_case_number` | nullable, string, max 255 |
- **Side effects:** `created_by` = current user, `updated_by` = null, `status` defaults to `pending`.
- **Where it goes next:** the controller **redirects straight into Stage 2** (`crime.criminalCase.createAccused`) with a flash message — a case is not considered usable until at least one accused person is attached.

## Stage 2 — Add Accused (mandatory next step)

- **Route:** `GET crime/criminalCase/{id}/createAccused` → delegates to `AccusedController`; `POST crime/accused` (`crime.accused.store`)
- **Controller:** `CriminalCaseController::createAccused` (fetch/guard) then `AccusedController::store`
- **View:** `oag.accused.create`, pre-loaded with `cases`, `islands`, `offencesByCategory` and the triggering case pre-selected (`selected_case_id`)
- **Actor:** any authenticated user
- **Fields captured:**
  | Field | Rule |
  |---|---|
  | `case_id` | required, must exist in `cases` |
  | `first_name`, `last_name` | required, string, max 255 |
  | `gender` | required, one of `Male,Female,Other` |
  | `date_of_birth` | required, date |
  | `age` | required, string, max 3 |
  | `address`, `contact` | nullable, string |
  | `phone` | nullable, string, max 20 |
  | `island_id` | required, must exist in `islands` |
- **Side effects:** `created_by` auto-injected. No case status change.
- **Branching on submit** (buttons in the form control the redirect):
  - "Continue to victim" → Stage 3, pre-scoped to this case
  - "Add another accused" → back to Stage 2 for the same case
  - otherwise → Accused index

## Stage 3 — Add Victim (optional, usually chained from Stage 2)

- **Route:** `GET crime/criminalCase/{id}/create-victim` (`crime.criminalCase.createVictim`) → `VictimController::createForCase`; `POST crime/victim` (`crime.victim.store`)
- **View:** `oag.victim.create`
- **Actor:** any authenticated user
- **Fields captured:**
  | Field | Rule |
  |---|---|
  | `case_id` | required, must exist in `cases` |
  | `first_name`, `last_name` | required, string, max 255 |
  | `gender` | required, one of `Male,Female,Other` |
  | `age` | required, string, max 10 |
  | `date_of_birth` | required, date |
  | `island_id` | required, must exist in `islands` |
  | `age_group` | nullable, one of `Under 13, Under 15, Under 18, Above 18` |
  | `address`, `contact`, `phone` | nullable |
- **Side effects:** none on the case record itself; redirects to the Criminal Case index on success.

## Stage 4 — Add Incident (optional, independent entry point)

- **Route:** `GET crime/criminalCase/{id}/create-incident` (`crime.criminalCase.createIncident`) → `IncidentController::createForCase`; `POST crime/incident` (`crime.incident.store`)
- **View:** `oag.incident.create`
- **Actor:** any authenticated user
- **Fields captured:**
  | Field | Rule |
  |---|---|
  | `case_id` | required, must exist in `cases` |
  | `lawyer_id` | required, must exist in `users` |
  | `island_id` | required, must exist in `islands` |
  | `date_of_incident_start` | required, date |
  | `date_of_incident_end` | nullable, date, must be ≥ start date |
  | `place_of_incident` | required, string, max 255 |
- **Side effects:** none on case status. Buttons allow "add another incident" (loop back to Stage 4) or "return to case" (Stage's `show` view).
- Not part of the mandatory chain — reached later via the "Related Records" menu on the case index, or directly.

## Stage 5 — Intake decision: Accept / Reject

Shown on the **Criminal Cases index** (`oag.crime.index`) as inline buttons, only for `cm.user`, and only while `status` is not already `accepted`, `rejected`, or `closed`.

- **Accept**
  - **Route:** `POST criminalCase/{id}/accept` (`crime.criminalCase.accept`)
  - **Controller:** `CriminalCaseController::accept`
  - **Guard:** `auth()->user()->hasRole('cm.user')`, else `403`
  - **Effect:** `status = accepted`, `rejection_reason` cleared.
- **Reject**
  - **Route:** `POST criminalCase/{id}/reject` (`crime.criminalCase.reject`)
  - **Controller:** `CriminalCaseController::reject`
  - **Form:** Bootstrap modal on the index page, single field `rejection_reason` (required, string)
  - **Guard:** `cm.user`, else `403`
  - **Effect:** `status = rejected`, `rejection_reason` stored.

## Stage 6 — Lawyer allocation

UI shows the "Case Allocation" menu item only for `cm.admin` and only when `status === 'pending'`.

- **Route:** `GET/POST crime/criminal-case/{id}/allocate` (`crime.criminalCase.allocateForm` / `allocateLawyer`)
- **Controller:** `CriminalCaseController::showAllocationForm` / `allocateLawyer`
- **View:** `oag.crime.allocate_lawyer`
- **Guard:** implicit via `Auth::user()` checked in the transaction (no explicit `hasRole` abort here — role is enforced only by hiding the UI link; see note below)
- **Fields captured:**
  | Field | Rule |
  |---|---|
  | `to_lawyer_id` | required, must exist in `users` |
  | `reallocation_reason` | nullable, string, max 1000 |
  | `reallocation_date` | required, date |
- **Logic:**
  - If the case currently has **no** `lawyer_id` → first allocation: writes nothing to `case_reallocations`, sets `lawyer_id`, `status = allocated`.
  - If the case **already has** a `lawyer_id` (e.g., reached this form outside the normal `pending`-only gate) → treated as a reallocation: inserts a `case_reallocations` row (`from_lawyer_id`, `to_lawyer_id`, reason, date), but **still sets `status = allocated`**, not `reallocated` (inconsistent with Stage 11 below — see companion doc §5.4).
- Wrapped in a `DB::transaction`; on failure, rolls back and returns with an error.

## Stage 7 — Case Review (evidence sufficiency decision)

UI shows the "Case Review" menu item only for `cm.user` and only when `status === 'accepted'`.

- **Routes:**
  - `GET crime/CaseReview/{id}/create` (`crime.CaseReview.create`) — `{id}` here is the **case id**
  - `POST crime/CaseReview/store` (`crime.CaseReview.store`)
- **Controller:** `CaseReviewController::create` / `store`
- **View:** `oag.crime.case_reviews.create`
- **Fields captured:**
  | Field | Rule |
  |---|---|
  | `case_id` | required, must exist in `cases` |
  | `evidence_status` | required, one of `pending_review, sufficient_evidence, insufficient_evidence, returned_to_police` |
  | `review_date` | required, date |
  | `reason_for_closure_id` | required **only if** evidence_status ∈ `{insufficient_evidence, returned_to_police}` |
  | `offence_id[]`, `category_id[]` | required **only if** evidence_status = `sufficient_evidence` (arrays, one offence+category pair per row) |
  | `offence_particulars` | required **only if** evidence_status = `sufficient_evidence` |
- **Two outcomes on submit:**
  1. **Sufficient evidence** → the selected `offence_id[]`/`category_id[]` pairs are synced onto the case via `case->offences()->syncWithoutDetaching(...)` (the `case_offence` pivot, carrying `category_id`). Case status is (re)set to `accepted`.
  2. **Insufficient evidence / returned to police** → `cases.date_file_closed` is stamped with today's date, and `status = closed`.
- **Constraint:** `case_reviews.case_id` is **unique** — only one review row can exist per case (the table models "the current review," not a history). Editing (`update`) can change `evidence_status` again later, and if it flips between the "closed" bucket and `sufficient_evidence`, the case's `date_file_closed`/`reason_for_closure_id` are reset accordingly (cleared when evidence becomes sufficient again, (re)stamped when it becomes insufficient).

## Stage 8 — Court Case (High Court proceedings)

UI shows the "Court Case" menu item only for `cm.user` and only when `status === 'accepted'`.

- **Routes:** `GET CourtCase/{id}/create` (`crime.CourtCase.create`), resource routes under `court-cases` for store/show/edit/update/destroy
- **Controller:** `CourtCaseController`
- **View:** `oag.court_cases.*`
- **Fields captured:**
  | Field | Rule |
  |---|---|
  | `case_id` | required, must exist in `cases` |
  | `charge_file_dated` | required, date |
  | `high_court_case_number` | nullable, string, max 255 |
  | `verdict` | nullable, one of `guilty, not_guilty, dismissed, withdrawn, other` |
  | `judgment_delivered_date` | nullable, date |
  | `court_outcome` | nullable, one of `win, lose` |
  | `decision_principle_established` | nullable, text |
- **Side effects:** none on `cases.status` — this is purely an append-only record of the High Court proceeding, visible later under "Related Records → Court Cases" on the index.

## Stage 9 — Appeal (`appeal_details`)

Unlike Stages 7–8, the "Appeal" menu item is shown for `cm.user` **unconditionally** (not gated by case status).

- **Routes:** `GET appeal/create/{id?}` (`crime.appeal.create`), resource routes under `appeal`
- **Controller:** `AppealDetailController`
- **View:** `oag.crime.appeal_details.*`
- **Create-form behavior:**
  - If launched with a case id, it pre-fills a suggested `case_name` ("Appeal - {original name}"), `island_id`, `lawyer_id` from the original case, and is *supposed* to block appealing a case twice via `is_appeal_case`/`is_on_appeal` flags (these columns don't currently exist on `cases` — see companion doc §5.6, so this guard is effectively inert today).
  - If launched with no id, offers a dropdown of "non-appeal" cases (`CriminalCaseRepository::getNonAppealCases()` — currently returns *all* cases since its filtering `where()` clauses are commented out).
- **Fields captured:**
  | Field | Rule |
  |---|---|
  | `case_id` | required, must exist in `cases` |
  | `appeal_case_number` | required, string |
  | `filing_date_type` | required, `court` or `defendant` |
  | `filing_date_value` | required, date |
  | `court_outcome` | required, string |
  | `judgment_delivered_date` | nullable, date |
  | `verdict` | required, string |
  | `decision_principle_established` | nullable, string |
- **Side effect on submit:** `filing_date_type = court` writes to `appeal_filing_date`; `filing_date_type = defendant` attempts to write to `appeal_filing_received_date`, a column that doesn't exist on `appeal_details` — this branch will error out (companion doc §5.7). No change to `cases.status` (explicitly left commented out in the code).

## Stage 10 — Court of Appeal (`court_of_appeals`)

Also shown unconditionally for `cm.user`, as a separate menu item from Stage 9's "Appeal."

- **Routes:** `GET courtOfAppeal/create/{caseId?}` (`crime.courtOfAppeal.create`), resource routes under `courtOfAppeal`
- **Controller:** `CourtOfAppealController`
- **View:** `oag.crime.court_of_appeals.*`
- **Fields captured:**
  | Field | Rule |
  |---|---|
  | `case_id` | required, must exist in `cases` |
  | `appeal_case_number` | nullable, string |
  | `appeal_filing_date` | required, date |
  | `filing_date_source` | required, string |
  | `judgment_delivered_date` | nullable, date |
  | `court_outcome` | nullable, one of `win, lose, remand` |
  | `decision_principle_established` | nullable, text |
- **Side effects:** none on `cases.status`. This is the final tier of appeal, distinct from the High Court–adjacent `appeal_details` table — the module effectively tracks three separate litigation-stage tables (`court_cases`, `appeal_details`, `court_of_appeals`) against the same case, each independently viewable from the case index's "Related Records" dropdown with a ✓ badge if a record exists.

## Stage 11 — Reallocation (can happen at any point after allocation)

Two independent entry points exist for changing the assigned lawyer after the fact:

**11a. Dedicated reallocation form** (the one exposed on the index, gated to `cm.admin` and `status === 'rejected'` in the JS — note: gating on `rejected` here looks like it should probably be `allocated`/`reallocated`, worth double-checking against intended business rules):
- **Routes:** `GET/POST criminalCase/{id}/reallocate` (`crime.criminalCase.showReallocationForm` / `crime.criminalCase.reallocate`)
- **Controller:** `CriminalCaseController::showReallocationForm` / `reallocateCase`
- **View:** `oag.crime.reallocate`
- **Guard:** `Auth::user()->hasRole('cm.admin')`, else `403`
- **Fields:** `to_lawyer_id` (required, exists in users), `reallocation_reason` (required, string), `reallocation_date` (required, date)
- **Effect:** inserts a `case_reallocations` row (capturing `from_lawyer_id` = the case's current lawyer), updates `cases.lawyer_id`, sets `status = reallocated`. Wrapped in `DB::transaction`.

**11b. Standalone Case Reallocation CRUD module** (`CaseReallocationController`, routes not shown wired into `web.php` in the excerpt reviewed — appears to be a general-purpose CRUD screen over `case_reallocations`, separate from the case-scoped flow above):
- Validates `case_id`, `from_lawyer_id`, `to_lawyer_id` (must differ from `from_lawyer_id`), `reallocation_reason`, `reallocation_date`.
- Does **not** touch `cases.status` or `cases.lawyer_id` at all — it only writes the `case_reallocations` row. If this screen is used instead of 11a, the case's actual `lawyer_id`/`status` will drift out of sync with the reallocation history.

**11c. Side-effect reallocation via plain case edit:**
- **Route:** `PUT criminalCase/{id}` (`crime.criminalCase.update`, the standard edit-case form)
- If the submitted `lawyer_id` differs from the case's current `lawyer_id`, the controller sets `status = 'reallocate'` (typo — not a valid enum value, not the same as 11a's `'reallocated'') and saves. **No `case_reallocations` row is written** on this path at all — the lawyer just changes with no audit trail, and the status write itself is likely to fail against the DB enum constraint.

---

## Quick reference: route → controller → view map

| Stage | Route name | Controller@method | View |
|---|---|---|---|
| 1 | `crime.criminalCase.create` / `.store` | `CriminalCaseController@create/store` | `oag.crime.create` |
| 2 | `crime.criminalCase.createAccused` / `crime.accused.store` | `CriminalCaseController@createAccused`, `AccusedController@store` | `oag.accused.create` |
| 3 | `crime.criminalCase.createVictim` / `crime.victim.store` | `VictimController@createForCase/store` | `oag.victim.create` |
| 4 | `crime.criminalCase.createIncident` / `crime.incident.store` | `IncidentController@createForCase/store` | `oag.incident.create` |
| 5 | `crime.criminalCase.accept` / `.reject` | `CriminalCaseController@accept/reject` | (modal on `oag.crime.index`) |
| 6 | `crime.criminalCase.allocateForm` / `.allocateLawyer` | `CriminalCaseController@showAllocationForm/allocateLawyer` | `oag.crime.allocate_lawyer` |
| 7 | `crime.CaseReview.create` / `.store` | `CaseReviewController@create/store` | `oag.crime.case_reviews.create` |
| 8 | `crime.CourtCase.create` + `court-cases.*` | `CourtCaseController` | `oag.court_cases.*` |
| 9 | `crime.appeal.create` + `appeal.*` | `AppealDetailController` | `oag.crime.appeal_details.*` |
| 10 | `crime.courtOfAppeal.create` + `courtOfAppeal.*` | `CourtOfAppealController` | `oag.crime.court_of_appeals.*` |
| 11a | `crime.criminalCase.showReallocationForm` / `.reallocate` | `CriminalCaseController@showReallocationForm/reallocateCase` | `oag.crime.reallocate` |
| 11b | `caseReallocation.*` (CRUD) | `CaseReallocationController` | `oag.crime.reallocations.*` |
| 11c | `crime.criminalCase.update` (edit form) | `CriminalCaseController@update` | `oag.crime.edit` |

For the list of concrete bugs referenced inline above (broken relation, invalid enum writes, missing columns, route-name typos, missing server-side role checks), see [`criminal-case-workflow.md` §5](criminal-case-workflow.md).
