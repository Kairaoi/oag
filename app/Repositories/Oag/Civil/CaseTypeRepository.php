<?php

namespace App\Repositories\Oag\Civil;

use App\Models\OAG\Civil\CaseType; // Update namespace according to your application's structure
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class CaseTypeRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CaseType::class; // Specify the model class for CaseType
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
        // Start a query on the case_types table and join the users table
        $dataTableQuery = $this->getModelInstance()->newQuery()
            ->leftJoin('users as createdBy', 'case_types.created_by', '=', 'createdBy.id') // Use left join for created_by
            ->leftJoin('users as updatedBy', 'case_types.updated_by', '=', 'updatedBy.id') // Use left join for updated_by
            ->select(
                'case_types.*',
                'createdBy.name as created_by_name', // Rename for clarity
                'updatedBy.name as updated_by_name'  // Rename for clarity
            );

        // Apply search filters if needed
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(createdBy.name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(updatedBy.name) LIKE ?', [$search]);
            });
        }

        // Handle soft deletes if required
        if ($trashed) {
            $dataTableQuery->onlyTrashed();
        }

        // Apply sorting if needed
        if (!empty($order_by)) {
            $dataTableQuery->orderBy($order_by, $sort);
        }

        // Return the results
        return $dataTableQuery->get();
    }

    /**
     * Get a collection of case type names indexed by their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('name', 'id'); // Adjusted to pluck names instead
    }
}
