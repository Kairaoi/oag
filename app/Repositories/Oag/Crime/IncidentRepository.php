<?php

namespace App\Repositories\Oag\Crime;

use App\Models\Oag\Crime\Incident;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class IncidentRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return Incident::class;
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
            ->leftJoin('cases', 'incidents.case_id', '=', 'cases.id')
            ->leftJoin('users as lawyers', 'incidents.lawyer_id', '=', 'lawyers.id')
            ->leftJoin('islands', 'incidents.island_id', '=', 'islands.id')
            ->select('incidents.*', 'cases.case_file_number', 'lawyers.name as lawyer_name', 'islands.island_name')
            ->distinct(); // Ensure distinct rows
    
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(incidents.place_of_incident) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(cases.case_file_number) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(lawyers.name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(islands.island_name) LIKE ?', [$search]);
            });
        }
    
        if ($trashed) {
            $dataTableQuery->onlyTrashed();
        }
    
        if (!empty($order_by)) {
            $validOrderBy = ['id', 'case_id', 'lawyer_id', 'island_id', 'place_of_incident', 'date_of_incident_start', 'date_of_incident_end'];
            if (in_array($order_by, $validOrderBy)) {
                $dataTableQuery->orderBy($order_by, $sort);
            }
        }
    
        return $dataTableQuery->get();
    }
    

    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('place_of_incident', 'id');
    }
}
