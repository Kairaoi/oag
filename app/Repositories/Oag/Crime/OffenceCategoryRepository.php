<?php

namespace App\Repositories\Oag\Crime;

use App\Models\OAG\Crime\OffenceCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class OffenceCategoryRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return OffenceCategory::class;
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
     * Update a model record in the database.
     *
     * @param int $id
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(int $id, array $data): Model
    {
        // Use the parent's update method
        return parent::update($id, $data);
    }

    /**
     * Retrieve data for DataTables with optional search, order, and trashed filtering.
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
        $dataTableQuery = $this->getModelInstance()->newQuery();

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->where('category_name', 'LIKE', $search);
            });
        }

        if ($trashed === true) {
            $dataTableQuery->onlyTrashed();
        }

        if (!empty($order_by)) {
            $dataTableQuery->orderBy($order_by, $sort);
        }

        return $dataTableQuery->get();
    }

    public function pluck(): \Illuminate\Support\Collection
{
    return $this->getModelInstance()->pluck('category_name', 'id');
}

}
