<?php

namespace App\Repositories\Oag\Crime;

use App\Models\Oag\Crime\Council;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class CouncilRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return Council::class;
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
    public function getForDataTable($search = '', $order_by = 'council_name', $sort = 'asc', $trashed = false): Collection
    {
        $dataTableQuery = $this->getModelInstance()->newQuery();
    
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->where('council_name', 'LIKE', $search);
            });
        }
    
        // Remove or adjust trashed logic if necessary
        // if ($trashed) {
        //     $dataTableQuery->onlyTrashed();
        // }
    
        if (!empty($order_by)) {
            $dataTableQuery->orderBy($order_by, $sort);
        }
    
        return $dataTableQuery->get();
    }

    /**
     * Pluck the council names for use in dropdowns or similar.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('council_name', 'id');
    }
}
