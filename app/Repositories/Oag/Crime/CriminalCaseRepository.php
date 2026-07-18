<?php

namespace App\Repositories\Oag\Crime;

use App\Models\OAG\Crime\CriminalCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

use DB;

class CriminalCaseRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CriminalCase::class;
    }

    

    /**
     * Count the number of specified model records in the database.
     *
     * @return int
     */
    public function count(): int
    {
        return parent::count();
    }

   /**
 * Create a new model record in the database.
 *
 * @param array $data
 *
 * @return \Illuminate\Database\Eloquent\Model
 */
public function create(array $data): Model
{
    return parent::create($data);
}

public function update(int $id, array $data): Model
{
    $model = $this->getModelInstance()->find($id);

    if (!$model) {
        throw new \Exception("Model not found.");
    }

    $model->update($data); // <- This line is doing the update

    return $model;
}

    

public function getForDataTable($search = '', $order_by = '', $sort = 'asc', $trashed = false)
{
    $user = auth()->user();

    // Subquery to get the latest case_review for each case
    $latestReviewSubquery = DB::table('case_reviews as cr1')
        ->select('cr1.case_id', 'cr1.evidence_status', 'cr1.review_date', 'cr1.date_file_closed', 'cr1.reason_for_closure_id', 'cr1.closure_decision')
        ->whereRaw('cr1.review_date = (SELECT MAX(cr2.review_date) FROM case_reviews cr2 WHERE cr2.case_id = cr1.case_id)')
        ->groupBy('cr1.case_id', 'cr1.evidence_status', 'cr1.review_date', 'cr1.date_file_closed', 'cr1.reason_for_closure_id', 'cr1.closure_decision');

    $query = $this->getModelInstance()->newQuery()
        ->leftJoin('users', 'cases.lawyer_id', '=', 'users.id')
        ->leftJoin('islands', 'cases.island_id', '=', 'islands.id')
        ->leftJoinSub($latestReviewSubquery, 'latest_review', 'latest_review.case_id', '=', 'cases.id')
        ->leftJoin('reasons_for_closure', 'latest_review.reason_for_closure_id', '=', 'reasons_for_closure.id')
        ->select([
            'cases.id as id',
            'cases.case_file_number',
            'cases.case_name',
            'cases.status',
            'cases.date_file_received',
            'cases.date_of_incident',
            'cases.deleted_at',
            'users.name as lawyer_name',
            'islands.island_name',
            'latest_review.evidence_status',
            'latest_review.review_date',
            'latest_review.date_file_closed',
            'latest_review.closure_decision',
            'reasons_for_closure.reason_description',
            DB::raw('(SELECT COUNT(*) FROM case_reviews WHERE case_reviews.case_id = cases.id AND case_reviews.deleted_at IS NULL) as reviewed_count'),
            DB::raw('(SELECT COUNT(*) FROM court_cases WHERE court_cases.case_id = cases.id AND court_cases.deleted_at IS NULL) as court_case_count'),
            DB::raw('(SELECT COUNT(*) FROM appeal_details WHERE appeal_details.case_id = cases.id AND appeal_details.deleted_at IS NULL) as appeal_count'),
            DB::raw('(SELECT COUNT(*) FROM court_of_appeals WHERE court_of_appeals.case_id = cases.id AND court_of_appeals.deleted_at IS NULL) as court_of_appeal_count'),
            DB::raw('(SELECT COUNT(*) FROM accused WHERE accused.case_id = cases.id AND accused.deleted_at IS NULL) as accused_count'),
            DB::raw("(SELECT GROUP_CONCAT(CONCAT(first_name, ' ', last_name) SEPARATOR ', ') FROM accused WHERE accused.case_id = cases.id AND accused.deleted_at IS NULL) as accused_names"),
            DB::raw("(SELECT GROUP_CONCAT(CONCAT(first_name, ' ', last_name) SEPARATOR ', ') FROM victims WHERE victims.case_id = cases.id AND victims.deleted_at IS NULL) as victim_names"),
            DB::raw('(SELECT COUNT(*) FROM court_cases WHERE court_cases.case_id = cases.id AND court_cases.deleted_at IS NULL AND court_cases.judgment_delivered_date IS NULL) as open_court_case_count'),
            DB::raw('(SELECT COUNT(*) FROM court_cases WHERE court_cases.case_id = cases.id AND court_cases.deleted_at IS NULL AND court_cases.judgment_delivered_date IS NOT NULL) as judged_court_case_count'),
            DB::raw('(SELECT appeal_status FROM appeal_details WHERE appeal_details.case_id = cases.id AND appeal_details.deleted_at IS NULL ORDER BY appeal_details.id DESC LIMIT 1) as latest_appeal_status'),
            DB::raw('(SELECT COUNT(*) FROM appeal_details WHERE appeal_details.case_id = cases.id AND appeal_details.deleted_at IS NULL AND appeal_details.judgment_delivered_date IS NOT NULL) as judged_appeal_count'),
            DB::raw('(SELECT COUNT(*) FROM court_of_appeals WHERE court_of_appeals.case_id = cases.id AND court_of_appeals.deleted_at IS NULL AND court_of_appeals.judgment_delivered_date IS NULL) as open_court_of_appeal_count'),
            DB::raw('(SELECT COUNT(*) FROM court_of_appeals WHERE court_of_appeals.case_id = cases.id AND court_of_appeals.deleted_at IS NULL AND court_of_appeals.judgment_delivered_date IS NOT NULL) as judged_court_of_appeal_count'),
            DB::raw('(SELECT COUNT(*) FROM court_cases WHERE court_cases.case_id = cases.id AND court_cases.deleted_at IS NULL AND court_cases.is_appealed = 1) as flagged_appealed_court_case_count'),
            DB::raw('(SELECT COUNT(*) FROM court_cases WHERE court_cases.case_id = cases.id AND court_cases.deleted_at IS NULL AND court_cases.judgment_delivered_date IS NOT NULL AND court_cases.is_appealed = 0) as confirmed_not_appealed_court_case_count'),
            DB::raw('(SELECT ag_decision FROM ag_reviews WHERE ag_reviews.case_id = cases.id AND ag_reviews.deleted_at IS NULL ORDER BY ag_reviews.id DESC LIMIT 1) as latest_ag_decision'),
            DB::raw('(SELECT COUNT(*) FROM registry_dispatches WHERE registry_dispatches.case_id = cases.id AND registry_dispatches.deleted_at IS NULL) as registry_dispatch_count'),
        ]);

    // 🔐 Role-based filtering: a plain cm.user (lawyer) can see only unassigned +
    // self-assigned cases. cm.admin accounts (even if they also hold cm.user)
    // always see every case.
    if ($user->hasRole('cm.user') && !$user->hasRole('cm.admin')) {
        $query->where(function ($q) use ($user) {
            $q->whereNull('cases.lawyer_id')
              ->orWhere('cases.lawyer_id', $user->id);
        });
    }

    // 🔍 Search functionality
    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%';
        $query->where(function ($q) use ($search) {
            $q->whereRaw('LOWER(cases.case_file_number) LIKE ?', [$search])
              ->orWhereRaw('LOWER(cases.case_name) LIKE ?', [$search])
              ->orWhereRaw('LOWER(cases.date_file_received) LIKE ?', [$search])
              ->orWhereRaw('LOWER(cases.date_of_incident) LIKE ?', [$search])
              ->orWhereRaw('LOWER(users.name) LIKE ?', [$search])
              ->orWhereRaw('LOWER(islands.island_name) LIKE ?', [$search])
              ->orWhereRaw('LOWER(latest_review.evidence_status) LIKE ?', [$search])
              ->orWhereRaw('LOWER(reasons_for_closure.reason_description) LIKE ?', [$search]);
        });
    }

    if ($trashed) {
        $query->withTrashed();
    }

    // Sorting
    if (!empty($order_by)) {
        $query->orderBy($order_by, $sort);
    } else {
        $query->orderBy('cases.id', 'desc');
    }

    $query->distinct();

    return $query->get()->map(function ($row) {
        $row->case_status = $this->determineCaseStatus($row);
        return $row;
    });
}

/**
 * Roll a case's court/appeal/review activity up into a single
 * human-readable status, most-advanced stage first: a court of appeal
 * judgment outranks a court judgment, which outranks a case_reviews
 * closure, etc.
 */
private function determineCaseStatus($row): string
{
    if ($row->judged_court_of_appeal_count > 0) {
        return 'Completed';
    }

    if ($row->open_court_of_appeal_count > 0) {
        // Escalated beyond the ordinary Appeal stage — give it its own label
        // so the status visibly advances as the case moves through the
        // workflow instead of staying stuck on the same "Pending" text.
        return 'Court of Appeal';
    }

    if ($row->appeal_count > 0) {
        // appeal_status is the authoritative signal once someone has set it:
        // "appealed" means the appeal itself has been escalated to the Court
        // of Appeal (no formal record there yet), "dismissed" and "withdrawn"
        // are terminal outcomes at this level. Only when it's still sitting at
        // "pending" (or unset) does the judgment date matter, to distinguish
        // "awaiting a hearing" from "judged, awaiting a status update".
        if ($row->latest_appeal_status === 'appealed') {
            return 'Court of Appeal';
        }

        if ($row->latest_appeal_status === 'dismissed') {
            return 'Completed';
        }

        if ($row->latest_appeal_status === 'withdrawn') {
            return 'Appeal Withdrawn';
        }

        return $row->judged_appeal_count > 0 ? 'Judgment Delivered' : 'Appealed';
    }

    if ($row->flagged_appealed_court_case_count > 0) {
        // The lawyer has flagged the court case as appealed, but no formal
        // appeal_details record has been filed yet — treat it as still active
        // rather than reporting the case as finished.
        return 'Appealed';
    }

    if ($row->judged_court_case_count > 0) {
        // A judgment being delivered doesn't finish the case on its own — the
        // losing side can still appeal. Only report "Completed" once someone
        // has confirmed via the "Not Appealed" checkbox that no appeal is
        // coming; otherwise the case is still awaiting that decision.
        return $row->confirmed_not_appealed_court_case_count > 0 ? 'Completed' : 'Judgment Delivered';
    }

    if ($row->open_court_case_count > 0) {
        return 'Pending in Court';
    }

    // A court case can now only be filed once the case has been dispatched
    // (see AuthorizesCriminalCase::assertCaseIsDispatched()), so a dispatch
    // with no court case yet is a genuine, brief in-between stage.
    if ($row->registry_dispatch_count > 0) {
        return 'Dispatched';
    }

    if ($row->latest_ag_decision === 'approved') {
        return 'AG Approved';
    }

    if ($row->latest_ag_decision === 'pending') {
        return 'Submitted to AG';
    }

    if ($row->latest_ag_decision === 'rejected') {
        // Not a dead end — the case stays 'accepted' so the lawyer can revise
        // and resubmit (AgReviewController::update()), same loop as an AG
        // rejection in the source workflow doc.
        return 'Returned for Revision';
    }

    if ($row->status === 'rejected') {
        return 'Rejected';
    }

    if (!empty($row->date_file_closed)) {
        // closure_decision is the newer, more specific signal (Step 8) — fall
        // back to the generic label for older rows that predate it.
        return $row->closure_decision ? 'Closed — Insufficient Evidence' : 'Completed';
    }

    if ($row->evidence_status === 'returned_to_police') {
        // Sent back to Police for further action — reopened as 'accepted'
        // rather than closed (see CaseReviewController), so without this
        // check it would otherwise fall through to "Reviewed" below.
        return 'Returned — Further Action Required';
    }

    if ($row->status === 'accepted') {
        // Mirrors the "Case Review" checkmark in the workflow dropdown: once a
        // review has been submitted, the case has moved past plain "accepted"
        // even though no court case exists yet.
        return $row->reviewed_count > 0 ? 'Reviewed' : 'Under Review';
    }

    if (in_array($row->status, ['allocated', 'reallocated'], true)) {
        return 'Allocated';
    }

    return 'Pending Allocation';
}




    
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('case_name', 'id');
    }
  
    /**
 * Get cases that are not appeals and not already on appeal
 * 
 * @return array
 */
public function getNonAppealCases()
{
    return $this->model
        ->where('is_appeal_case', false)
        ->where('is_on_appeal', false)
        ->pluck('case_name', 'id')
        ->toArray();
}

}
