<?php

namespace App\Repositories\Oag\Crime;

use App\Models\OAG\Crime\CaseReallocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;


class CaseReallocationRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CaseReallocation::class;
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
     * Get data for DataTables with optional search and sorting.
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @param bool $trashed
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForDataTable($search = '', $order_by = '', $sort = 'asc', $trashed = false): Collection
    {
        $query = $this->getModelInstance()->newQuery()
            ->join('users as from_lawyer', 'case_reallocations.from_lawyer_id', '=', 'from_lawyer.id')
            ->join('users as to_lawyer', 'case_reallocations.to_lawyer_id', '=', 'to_lawyer.id')
            ->join('cases', 'case_reallocations.case_id', '=', 'cases.id')
            ->join('users as creator', 'case_reallocations.created_by', '=', 'creator.id')
            ->select(
                'case_reallocations.*',
                'cases.case_name',
                'from_lawyer.name as from_lawyer_name',
                'to_lawyer.name as to_lawyer_name',
                'creator.name as created_by_name'
            );

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(cases.case_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(from_lawyer.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(to_lawyer.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(creator.name) LIKE ?', [$search]);
            });
        }

        if ($trashed) {
            $query->onlyTrashed();
        }

        if (!empty($order_by)) {
            $query->orderBy($order_by, $sort);
        }

        return $query->get();
    }

    /**
     * Pluck case names with their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('case_name', 'id');
    }
}
