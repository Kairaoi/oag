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
        ->select('cr1.case_id', 'cr1.evidence_status', 'cr1.review_date', 'cr1.date_file_closed', 'cr1.reason_for_closure_id')
        ->whereRaw('cr1.review_date = (SELECT MAX(cr2.review_date) FROM case_reviews cr2 WHERE cr2.case_id = cr1.case_id)')
        ->groupBy('cr1.case_id', 'cr1.evidence_status', 'cr1.review_date', 'cr1.date_file_closed', 'cr1.reason_for_closure_id');

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
            'reasons_for_closure.reason_description',
            DB::raw('(SELECT COUNT(*) FROM case_reviews WHERE case_reviews.case_id = cases.id) as reviewed_count'),
            DB::raw('(SELECT COUNT(*) FROM court_cases WHERE court_cases.case_id = cases.id) as court_case_count'),
            DB::raw('(SELECT COUNT(*) FROM appeal_details WHERE appeal_details.case_id = cases.id) as appeal_count')
        ]);

    // 🔐 Role-based filtering: cm.user can see unassigned + self-assigned cases
    if ($user->hasRole('cm.user')) {
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

    return $query->get();
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
        // ->where('is_appeal_case', false)
        // ->where('is_on_appeal', false)
        ->pluck('case_name', 'id')
        ->toArray();
}

}
