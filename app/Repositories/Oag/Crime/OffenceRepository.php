<?php

namespace App\Repositories\Oag\Crime;

use App\Models\OAG\Crime\Offence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class OffenceRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return Offence::class;
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
     * Update the specified model record in the database.
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForDataTable($search = '', $order_by = '', $sort = 'asc', $trashed = false)
    {
        $dataTableQuery = $this->getModelInstance()
            ->with('offenceCategory') // Eager load the offenceCategory relationship
            ->newQuery();
    
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->where('offence_name', 'LIKE', $search);
            });
        }
    
        if ($trashed === true) {
            $dataTableQuery->onlyTrashed();
        }
    
        if (!empty($order_by)) {
            $dataTableQuery->orderBy($order_by, $sort);
        }
    
        $results = $dataTableQuery->get();
    
        // Map the results to include the offence_id, offence_name, and category_name
        return $results->map(function ($offence) {
            return [
                'id' => $offence->id,
                'offence_name' => $offence->offence_name,
                'category_name' => $offence->offenceCategory ? $offence->offenceCategory->category_name : 'N/A',
                'created_at' => $offence->created_at ? $offence->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $offence->updated_at ? $offence->updated_at->format('Y-m-d H:i:s') : null,
                'deleted_at' => $offence->deleted_at ? $offence->deleted_at->format('Y-m-d H:i:s') : null,
            ];
        });
    }
    
    public function pluck(): \Illuminate\Support\Collection
    {
        // Get all offences with their categories
        $offences = $this->getModelInstance()
            ->with('offenceCategory') // Eager load offenceCategory relationship
            ->get();
    
        // Group offences by category
        $groupedOffences = $offences->groupBy(function ($offence) {
            return $offence->offenceCategory ? $offence->offenceCategory->category_name : 'Uncategorized';
        });
    
        // Transform the grouped collection to a pluckable format
        return $groupedOffences->map(function ($offences, $category) {
            return $offences->pluck('offence_name', 'id');
        });
    }
    
}
