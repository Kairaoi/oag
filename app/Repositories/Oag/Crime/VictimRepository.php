<?php

namespace App\Repositories\Oag\Crime;

use App\Models\OAG\Crime\Victim;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class VictimRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return Victim::class;
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
        // Create a new query instance for the model
        $dataTableQuery = $this->getModelInstance()->newQuery();
    
        // Join the users table to get the creator's name
        $dataTableQuery->leftJoin('users', 'victims.created_by', '=', 'users.id')
                       ->select('victims.*', 'users.name as created_by_name');
    
        // Apply search filters
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(victims.first_name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(victims.last_name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(victims.gender) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(victims.age_group) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(victims.age) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(users.name) LIKE ?', [$search]);
            });
        }
    
        // Apply trashed logic if needed
        if ($trashed) {
            $dataTableQuery->onlyTrashed();
        }
    
        // Apply ordering
        if (!empty($order_by)) {
            // Ensure $order_by is a valid column name
            $validOrderBy = ['id', 'case_id', 'island_id', 'first_name', 'last_name', 'gender', 'date_of_birth', 'age', 'age_group', 'created_by', 'updated_by', 'created_at', 'updated_at'];
            if (in_array($order_by, $validOrderBy)) {
                $dataTableQuery->orderBy($order_by, $sort);
            }
        }
    
        // Execute the query and return results
        return $dataTableQuery->get();
    }
    
    /**
     * Get a collection of victim names by their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('first_name', 'id');
    }
}
