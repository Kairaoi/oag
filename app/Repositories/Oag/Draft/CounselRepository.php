<?php

namespace App\Repositories\Oag\Draft;

use App\Models\OAG\Draft\Counsel;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\CustomBaseRepository;

class CounselRepository extends CustomBaseRepository
{
    /**
     * Return the model class that this repository handles.
     * 
     * @return string
     */
    public function model()
    {
        return Counsel::class;
    }

    /**
     * Count the number of Counsel records.
     * 
     * @return int
     */
    public function count(): int
    {
        return parent::count();
    }

    /**
     * Create a new Counsel record.
     * 
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return parent::create($data);
    }

    /**
     * Update an existing Counsel record by ID.
     * 
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        return parent::update($id, $data);
    }

    /**
     * Retrieve Counsel records for a DataTable.
     * 
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @param bool $trashed
     * @return \Illuminate\Support\Collection
     */
    public function getForDataTable($search = '', $order_by = '', $sort = 'asc', $trashed = false)
    {
        $dataTableQuery = $this->getModelInstance()->newQuery();

        // Apply a search filter if search term is provided
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(position) LIKE ?', [$search]);
            });
        }

        // If trashed is true, only return soft-deleted records
        if ($trashed) {
            $dataTableQuery->onlyTrashed();
        }

        // Order results by the specified column and sort direction
        if (!empty($order_by)) {
            $dataTableQuery->orderBy($order_by, $sort);
        }

        return $dataTableQuery->get();
    }

    /**
     * Retrieve a list of all Counsel records with their IDs as the key and names as the value.
     * 
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('name', 'id');
    }
}
