<?php

namespace App\Repositories\Oag\Crime;

use App\Models\Oag\Crime\CaseReview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class CaseReviewRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CaseReview::class;
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

    /**
     * Update an existing model record in the database.
     *
     * @param int $id
     * @param array $data
     *
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        return parent::update($id, $data);
    }

    /**
     * Get data for DataTables with optional search and sorting.
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @param bool $trashed
     *
     * @return Collection
     */
    public function getForDataTable($search = '', $order_by = '', $sort = 'asc', $trashed = false): Collection
{
    $dataTableQuery = $this->getModelInstance()->newQuery()
        ->leftJoin('cases', 'case_reviews.case_id', '=', 'cases.id')
        // Join with users table for the creator of the review
        ->leftJoin('users as creator', 'case_reviews.created_by', '=', 'creator.id')
        // Join with users table for the new lawyer (if reassigned)
        ->leftJoin('users as new_lawyer', 'case_reviews.new_lawyer_id', '=', 'new_lawyer.id')
        // Join with case_reallocations to get reallocation information
        ->leftJoin('case_reallocations', function($join) {
            $join->on('case_reviews.case_id', '=', 'case_reallocations.case_id')
                // Match when case_reviews indicates a reallocation
                ->where('case_reviews.action_type', 'reallocation');
        })
        // Join with users table for the from_lawyer in reallocations
        ->leftJoin('users as from_lawyer', 'case_reallocations.from_lawyer_id', '=', 'from_lawyer.id')
        // Join with users table for the to_lawyer in reallocations
        ->leftJoin('users as to_lawyer', 'case_reallocations.to_lawyer_id', '=', 'to_lawyer.id')
        ->select([
            'case_reviews.*',
            'cases.case_name',
            'creator.name as created_by_name',
            'new_lawyer.name as new_lawyer_name',
            'case_reallocations.reallocation_date',
            'from_lawyer.name as from_lawyer_name',
            'to_lawyer.name as to_lawyer_name',
            'case_reallocations.reallocation_reason as reallocation_details'
        ])
        ->distinct(); // To ensure distinct rows

    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%';
        $dataTableQuery->where(function ($query) use ($search) {
            $query->whereRaw('LOWER(case_reviews.review_notes) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(creator.name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(cases.case_name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(new_lawyer.name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(from_lawyer.name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(to_lawyer.name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(case_reviews.evidence_status) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(case_reviews.action_type) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(case_reallocations.reallocation_reason) LIKE ?', [$search]);
        });
    }

    if ($trashed) {
        $dataTableQuery->onlyTrashed();
    }

    if (!empty($order_by)) {
        $validOrderBy = [
            'id', 'case_id', 'created_by', 'review_notes', 'review_date', 
            'evidence_status', 'action_type', 'created_by_name', 'case_name',
            'new_lawyer_name', 'from_lawyer_name', 'to_lawyer_name', 
            'reallocation_date', 'reallocation_details'
        ];
        
        if (in_array($order_by, $validOrderBy)) {
            $dataTableQuery->orderBy($order_by, $sort);
        }
    }

    return $dataTableQuery->get();
}
    /**
     * Pluck a list of values for a given column.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('review_notes', 'id');
    }
}
