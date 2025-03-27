<?php

namespace App\Repositories\Oag\Crime;

use App\Models\Oag\Crime\Accused;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class AccusedRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return Accused::class;
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
        ->leftJoin('accused_offence', 'accused.id', '=', 'accused_offence.accused_id')
        ->leftJoin('offences', 'accused_offence.offence_id', '=', 'offences.id')
 
        ->leftJoin('offence_categories', 'offences.offence_category_id', '=', 'offence_categories.id')
        ->select('accused.*',  'offences.offence_name', 'offence_categories.category_name')
        ->distinct(); // To ensure distinct rows

    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%';
        $dataTableQuery->where(function ($query) use ($search) {
            $query->whereRaw('LOWER(accused.accused_particulars) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(accused.gender) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(accused.date_of_birth) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(accused.first_name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(accused.last_name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(users.name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(offences.offence_name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(offence_categories.category_name) LIKE ?', [$search]);
        });
    }

    if ($trashed) {
        $dataTableQuery->onlyTrashed();
    }

    if (!empty($order_by)) {
        $validOrderBy = ['id', 'case_id',  'first_name', 'last_name', 'accused_particulars', 'gender', 'date_of_birth', 'lawyer_name', 'offence_name', 'category_name'];
        if (in_array($order_by, $validOrderBy)) {
            $dataTableQuery->orderBy($order_by, $sort);
        }
    }

    return $dataTableQuery->get();
}





    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('first_name', 'id');
    }
}
