<?php

namespace App\Repositories\Oag\Civil;

use App\Models\OAG\Civil\CivilCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Repositories\CustomBaseRepository;

class CivilCaseRepository extends CustomBaseRepository
{
    public function model()
    {
        return CivilCase::class;
    }

    public function count(): int
    {
        return parent::count();
    }

    public function create(array $data): Model
    {
        // Log data to ensure it’s being received properly
        Log::info('Creating CivilCase record', ['data' => $data]);
        
        return parent::create($data);
    }

    public function update(int $id, array $data): Model
    {
        Log::info('Updating CivilCase record', ['id' => $id, 'data' => $data]);
        
        return parent::update($id, $data);
    }

    public function getForDataTable($search = '', $order_by = '', $sort = 'asc', $trashed = false)
{
    $dataTableQuery = $this->getModelInstance()->newQuery()
        ->leftJoin('users as createdBy', 'civil_cases.created_by', '=', 'createdBy.id')
        ->leftJoin('users as updatedBy', 'civil_cases.updated_by', '=', 'updatedBy.id')
        // Join case_counsels table to fetch the plaintiff and defendant data
        ->leftJoin('case_counsels as plaintiff_counsel', function ($join) {
            $join->on('civil_cases.id', '=', 'plaintiff_counsel.civil_case_id')
                 ->where('plaintiff_counsel.type', '=', 'Plaintiff');
        })
        ->leftJoin('users as plaintiff', 'plaintiff_counsel.user_id', '=', 'plaintiff.id')  // Ensure 'user_id' is correct
        ->leftJoin('case_counsels as defendant_counsel', function ($join) {
            $join->on('civil_cases.id', '=', 'defendant_counsel.civil_case_id')
                 ->where('defendant_counsel.type', '=', 'Defendant');
        })
        ->leftJoin('users as defendant', 'defendant_counsel.user_id', '=', 'defendant.id')  // Ensure 'user_id' is correct
        // Join case_types table to fetch the case type name
        ->leftJoin('case_types', 'civil_cases.case_type_id', '=', 'case_types.id')
        // Join court_categories table to fetch the court category name
        ->leftJoin('court_categories', 'civil_cases.court_category_id', '=', 'court_categories.id')
        ->select(
            'civil_cases.*',
            'createdBy.name as created_by_name',
            'updatedBy.name as updated_by_name',
            'plaintiff.name as plaintiff_name',
            'defendant.name as defendant_name',
            'case_types.name as case_type_name', // Adding case type name
            'court_categories.name as court_category_name' // Adding court category name
        );

    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%';
        $dataTableQuery->where(function ($query) use ($search) {
            $query->whereRaw('LOWER(createdBy.name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(updatedBy.name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(plaintiff.name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(defendant.name) LIKE ?', [$search]);
        });
    }

    if ($trashed) {
        $dataTableQuery->onlyTrashed();
    }

    if (!empty($order_by)) {
        $dataTableQuery->orderBy($order_by, $sort);
    }

    return $dataTableQuery->get();
}


    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('case_name', 'id');
    }
}
