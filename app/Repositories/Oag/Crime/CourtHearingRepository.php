<?php

namespace App\Repositories\Oag\Crime;

use App\Models\Oag\Crime\CourtHearing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class CourtHearingRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CourtHearing::class;
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
    $dataTableQuery = $this->getModelInstance()
        ->newQuery()
        ->leftJoin('cases', 'court_hearings.case_id', '=', 'cases.id')
        ->leftJoin('users as created_by_user', 'court_hearings.created_by', '=', 'created_by_user.id')
        ->leftJoin('users as updated_by_user', 'court_hearings.updated_by', '=', 'updated_by_user.id')
        ->select(
            'court_hearings.*',
            'cases.case_file_number',  // ✅ Correct field
            'created_by_user.name as created_by_name',
            'updated_by_user.name as updated_by_name'
        )
        
        ->distinct();

    // Handle soft deletes
    if ($trashed) {
        $dataTableQuery->onlyTrashed();
    } else {
        $dataTableQuery->whereNull('court_hearings.deleted_at'); // Important!
    }

    // Handle search
    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%';
        $dataTableQuery->where(function ($query) use ($search) {
            $query->whereRaw('LOWER(cases.case_number) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(court_hearings.hearing_type) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(court_hearings.verdict) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(created_by_user.name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(updated_by_user.name) LIKE ?', [$search]);
        });
    }

    // Handle ordering
    if (!empty($order_by)) {
        $orderableColumns = [
            'id' => 'court_hearings.id',
            'case_id' => 'court_hearings.case_id',
            'hearing_date' => 'court_hearings.hearing_date',
            'hearing_type' => 'court_hearings.hearing_type',
            'verdict' => 'court_hearings.verdict',
            'created_by_name' => 'created_by_user.name',
            'updated_by_name' => 'updated_by_user.name',
        ];

        if (array_key_exists($order_by, $orderableColumns)) {
            $dataTableQuery->orderByRaw($orderableColumns[$order_by] . ' ' . $sort);
        }
    }

    return $dataTableQuery->get();
}

    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('hearing_type', 'id');
    }
}
