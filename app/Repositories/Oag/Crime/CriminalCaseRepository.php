<?php

namespace App\Repositories\Oag\Crime;

use App\Models\OAG\Crime\CriminalCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class CriminalCaseRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CriminalCase::class;
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

public function update(int $id, array $data): Model
    {
        // Use the parent's update method
        return parent::update($id, $data);
    }
    
    public function getForDataTable($search = '', $order_by = '', $sort = 'asc', $trashed = false)
{
    // Start a query on the cases table and join the users, islands, and reasons_for_closure tables
    $dataTableQuery = $this->getModelInstance()->newQuery()
        ->join('users', 'cases.lawyer_id', '=', 'users.id')
        ->leftJoin('islands', 'cases.island_id', '=', 'islands.id') // Use left join to include cases even if there are no related islands
        ->leftJoin('reasons_for_closure', 'cases.reason_for_closure_id', '=', 'reasons_for_closure.id') // Use left join for reasons_for_closure
        ->select(
            'cases.*', 
            'users.name as lawyer_name', // Rename 'users.name' to 'lawyer_name' for clarity
            'islands.island_name', // Select the island name
            'reasons_for_closure.reason_description' // Select the reason description
        );

    // Apply search filters if needed
    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%';
        $dataTableQuery->where(function ($query) use ($search) {
            $query->whereRaw('LOWER(case_file_number) LIKE ?', [$search])
                ->orWhereRaw('LOWER(case_name) LIKE ?', [$search])
                ->orWhereRaw('LOWER(date_file_received) LIKE ?', [$search])
                ->orWhereRaw('LOWER(date_file_closed) LIKE ?', [$search])
                ->orWhereRaw('LOWER(users.name) LIKE ?', [$search])
                ->orWhereRaw('LOWER(islands.island_name) LIKE ?', [$search])
                ->orWhereRaw('LOWER(reasons_for_closure.reason_description) LIKE ?', [$search]);
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


    
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('case_file_number', 'id');
    }
  

}
