<?php

namespace App\Repositories\Oag\Crime;

use App\Models\OAG\Crime\CriminalCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

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
    
        $query = $this->getModelInstance()->newQuery()
            ->join('users', 'cases.lawyer_id', '=', 'users.id')
            ->leftJoin('islands', 'cases.island_id', '=', 'islands.id')
            ->leftJoin('case_reviews', 'case_reviews.case_id', '=', 'cases.id')
            ->leftJoin('reasons_for_closure', 'case_reviews.reason_for_closure_id', '=', 'reasons_for_closure.id')
            ->select([
                'cases.id as id',
                'cases.case_file_number',
                'cases.case_name',
                'cases.status', // ✅ Keep status field
                'cases.date_file_received',
                'cases.date_of_allocation',
                'cases.deleted_at',
                'users.name as lawyer_name',
                'islands.island_name',
                'case_reviews.evidence_status',
                'case_reviews.review_date',
                'case_reviews.date_file_closed',
                'reasons_for_closure.reason_description'
            ]);
    
        // 🔐 Apply access control based on role
        if ($user->hasRole('cm.user')) {
            $query->where('cases.lawyer_id', $user->id); // Show only cases assigned to the user
        }
    
        // 🔍 Optional search
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(cases.case_file_number) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(cases.case_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(cases.date_file_received) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(cases.date_of_allocation) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(users.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(islands.island_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(case_reviews.evidence_status) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(reasons_for_closure.reason_description) LIKE ?', [$search]);
            });
        }
    
        if ($trashed) {
            $query->withTrashed();
        }
    
        if (!empty($order_by)) {
            $query->orderBy($order_by, $sort);
        }
    
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
        ->where('is_appeal_case', false)
        ->where('is_on_appeal', false)
        ->pluck('case_name', 'id')
        ->toArray();
}

}
