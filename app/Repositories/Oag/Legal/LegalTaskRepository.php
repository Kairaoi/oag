<?php

namespace App\Repositories\Oag\Legal;

use App\Models\OAG\Legal\LegalTask; // Update namespace according to your application's structure
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class LegalTaskRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return LegalTask::class; // Specify the model class for LegalTask
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
        // Start a query on the legal_tasks table and join the users table for created_by, updated_by, and allocated_to
        $dataTableQuery = $this->getModelInstance()->newQuery()
            ->leftJoin('users as createdBy', 'legal_tasks.created_by', '=', 'createdBy.id') // Use left join for created_by
            ->leftJoin('users as updatedBy', 'legal_tasks.updated_by', '=', 'updatedBy.id') // Use left join for updated_by
            ->leftJoin('users as allocatedTo', 'legal_tasks.allocated_to', '=', 'allocatedTo.id') // Join for allocated_to user
            ->select(
                'legal_tasks.*',
                'createdBy.name as created_by_name', // Rename for clarity
                'updatedBy.name as updated_by_name', // Rename for clarity
                'allocatedTo.name as allocated_to_name', // Get the name of the user allocated to the task
                'legal_tasks.date_task_achieved', // Select the date_task_achieved column
                'legal_tasks.date_approved_by_ag', // Select the date_approved_by_ag column
                'legal_tasks.meeting_date' // Select the meeting_date column
            );
    
        // Apply search filters if needed
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(task) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(ministry) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(status) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(date_task_achieved) LIKE ?', [$search]) // Search for date_task_achieved
                      ->orWhereRaw('LOWER(date_approved_by_ag) LIKE ?', [$search]) // Search for date_approved_by_ag
                      ->orWhereRaw('LOWER(meeting_date) LIKE ?', [$search]); // Search for meeting_date
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
     * Get a collection of legal task names indexed by their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('task', 'id'); // Adjusted to pluck tasks instead
    }
}
