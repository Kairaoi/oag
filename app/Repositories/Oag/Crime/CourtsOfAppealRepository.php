<?php

namespace App\Repositories\Oag\Crime;

use App\Models\Oag\Crime\CourtsOfAppeal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class CourtsOfAppealRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CourtsOfAppeal::class;
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
            ->leftJoin('users as creators', 'courts_of_appeal.created_by', '=', 'creators.id')
            ->leftJoin('users as updaters', 'courts_of_appeal.updated_by', '=', 'updaters.id')
            ->select('courts_of_appeal.*', 'creators.name as created_by_name', 'updaters.name as updated_by_name')
            ->distinct();

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(courts_of_appeal.court_name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(courts_of_appeal.description) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(creators.name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(updaters.name) LIKE ?', [$search]);
            });
        }

        if ($trashed) {
            $dataTableQuery->onlyTrashed();
        }

        if (!empty($order_by)) {
            $validOrderBy = ['id', 'court_name', 'description', 'created_by', 'updated_by'];
            if (in_array($order_by, $validOrderBy)) {
                $dataTableQuery->orderBy($order_by, $sort);
            }
        }

        return $dataTableQuery->get();
    }

    /**
     * Pluck court names.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('court_name', 'id');
    }
}
