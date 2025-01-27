<?php

namespace App\Repositories\Oag\Civil;

use App\Models\OAG\Civil\CourtCategory; // Adjusted model name
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class CourtCategoryRepository extends CustomBaseRepository // Renamed repository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CourtCategory::class; // Updated model reference
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
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(int $id, array $data): Model
    {
        return parent::update($id, $data);
    }

    /**
     * Get data for DataTables.
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @param bool $trashed
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForDataTable($search = '', $order_by = '', $sort = 'asc', $trashed = false)
    {
        // Start a query on the civil_cases table and join related tables
        $dataTableQuery = $this->getModelInstance()->newQuery()
            ->leftJoin('court_categories as cc', 'civil_cases.court_category_id', '=', 'cc.id') // Join court categories
            ->leftJoin('case_types as ct', 'civil_cases.case_type_id', '=', 'ct.id') // Join case types
            ->leftJoin('users as createdBy', 'civil_cases.created_by', '=', 'createdBy.id') // Join users (created_by)
            ->leftJoin('users as updatedBy', 'civil_cases.updated_by', '=', 'updatedBy.id') // Join users (updated_by)
            ->select(
                'civil_cases.*',
                'cc.name as court_category_name',
                'ct.name as case_type_name',
                'createdBy.name as created_by_name',
                'updatedBy.name as updated_by_name'
            );

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(civil_cases.primary_number) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(civil_cases.case_name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(cc.name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(ct.name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(createdBy.name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(updatedBy.name) LIKE ?', [$search]);
            });
        }

        if ($trashed) {
            $dataTableQuery->onlyTrashed();
        }

        if (!empty($order_by)) {
            $dataTableQuery->orderBy($order_by, $sort);
        }

        return $dataTableQuery->get();
    }


    /**
     * Get a collection of court category names indexed by their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('name', 'id');
    }
}
