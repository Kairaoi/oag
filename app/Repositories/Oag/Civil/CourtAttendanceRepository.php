<?php

namespace App\Repositories\Oag\Civil;

use App\Models\OAG\Civil\CourtAttendance; // Update namespace according to your application's structure
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class CourtAttendanceRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CourtAttendance::class; // Specify the model class for CourtAttendance
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
    $dataTableQuery = $this->getModelInstance()->newQuery()
        ->leftJoin('users as createdBy', 'court_attendances.created_by', '=', 'createdBy.id')
        ->leftJoin('users as updatedBy', 'court_attendances.updated_by', '=', 'updatedBy.id')
        ->leftJoin('civil_cases', 'court_attendances.civil_case_id', '=', 'civil_cases.id')
        ->leftJoin('case_counsels as plaintiff', function ($join) {
            $join->on('civil_cases.id', '=', 'plaintiff.civil_case_id')
                 ->where('plaintiff.type', '=', 'Plaintiff');
        })
        ->leftJoin('users as plaintiffUser', 'plaintiff.user_id', '=', 'plaintiffUser.id') // Join to get plaintiff name
        ->leftJoin('case_counsels as defendant', function ($join) {
            $join->on('civil_cases.id', '=', 'defendant.civil_case_id')
                 ->where('defendant.type', '=', 'Defendant');
        })
        ->leftJoin('users as defendantUser', 'defendant.user_id', '=', 'defendantUser.id') // Join to get defendant name
        ->leftJoin('users as opposingCounsel', 'court_attendances.opposing_counsel_name', '=', 'opposingCounsel.id') // Join to get opposing counsel name
        ->select(
            'court_attendances.*',
            'createdBy.name as created_by_name',
            'updatedBy.name as updated_by_name',
            'civil_cases.case_name',
            'civil_cases.number',
            'plaintiffUser.name as plaintiff_name',  // Fetch plaintiff name
            'defendantUser.name as defendant_name',  // Fetch defendant name
            'opposingCounsel.name as opposing_counsel_name'  // Fetch opposing counsel name
        );

    // Apply search filters if needed
    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%';
        $dataTableQuery->where(function ($query) use ($search) {
            $query->whereRaw('LOWER(createdBy.name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(updatedBy.name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(plaintiffUser.name) LIKE ?', [$search]) // Search in plaintiff name
                  ->orWhereRaw('LOWER(defendantUser.name) LIKE ?', [$search]) // Search in defendant name
                  ->orWhereRaw('LOWER(opposingCounsel.name) LIKE ?', [$search]); // Search in opposing counsel name
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

    return $dataTableQuery->get();
}

    
    /**
     * Get a collection of court attendance hearing types indexed by their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('hearing_type', 'id'); // Adjusted to pluck hearing types
    }
}
