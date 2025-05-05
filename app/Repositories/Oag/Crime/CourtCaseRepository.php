<?php

namespace App\Repositories\Oag\Crime;

use App\Models\OAG\Crime\CourtCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\CustomBaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CourtCaseRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CourtCase::class;
    }

    /**
     * Create a new court case.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return parent::create($data);
    }

    /**
     * Update a specific court case.
     *
     * @param int $id
     * @param array $data
     * @return Model
     * @throws \Exception
     */
    public function update(int $id, array $data): Model
    {
        $model = $this->getModelInstance()->find($id);

        if (!$model) {
            throw new \Exception("Court case not found.");
        }

        $model->update($data);
        return $model;
    }

    /**
     * Count total court cases.
     *
     * @return int
     */
    public function count(): int
    {
        return parent::count();
    }

    /**
     * Get court cases for DataTables listing with optional filters.
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @param bool $trashed
     * @return Collection
     */
    public function getForDataTable($search = '', $order_by = '', $sort = 'asc', $trashed = false): Collection
    {
        $query = $this->getModelInstance()->newQuery()
            ->join('cases', 'court_cases.case_id', '=', 'cases.id')
            ->join('users as creator', 'court_cases.created_by', '=', 'creator.id')
            ->leftJoin('users as updater', 'court_cases.updated_by', '=', 'updater.id')
            ->select([
                'court_cases.id',
                'court_cases.charge_file_dated',
                'court_cases.high_court_case_number',
                'court_cases.court_outcome',
                'court_cases.court_outcome_details',
                'court_cases.court_outcome_date',
                'court_cases.judgment_delivered_date',
                'court_cases.verdict',
                'court_cases.decision_principle_established',
                'cases.case_name',
                'creator.name as created_by_name',
                'updater.name as updated_by_name',
                'court_cases.deleted_at'
            ]);

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(cases.case_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(court_cases.high_court_case_number) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(court_cases.court_outcome) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(court_cases.verdict) LIKE ?', [$search]);
            });
        }

        if ($trashed) {
            $query->withTrashed();
        }

        if (!empty($order_by)) {
            $query->orderBy($order_by, $sort);
        }

        return $query->get();
    }

    /**
     * Pluck court case names (or high court case numbers) for dropdowns.
     *
     * @return Collection
     */
    public function pluck(): Collection
    {
        return $this->getModelInstance()->pluck('high_court_case_number', 'id');
    }

    public function getCourtCasesByCaseId(int $caseId, bool $onlyTrashed = false): Collection
{
    // Disable ONLY_FULL_GROUP_BY for session (if needed)
    DB::statement("SET SQL_MODE=''");

    $query = $this->getModelInstance();

    if ($onlyTrashed) {
        $query = $query->onlyTrashed();
    }

    $results = $query
        ->leftJoin('cases', 'court_cases.case_id', '=', 'cases.id')
        ->leftJoin('users as creator', 'court_cases.created_by', '=', 'creator.id')
        ->leftJoin('users as updater', 'court_cases.updated_by', '=', 'updater.id')
        ->leftJoin('case_offence', 'court_cases.case_id', '=', 'case_offence.case_id')
        ->leftJoin('offences', 'case_offence.offence_id', '=', 'offences.id')
        ->leftJoin('offence_categories', 'offences.offence_category_id', '=', 'offence_categories.id')
        ->leftJoin('accused', 'court_cases.case_id', '=', 'accused.case_id')
        ->leftJoin('victims', 'court_cases.case_id', '=', 'victims.case_id')
        ->select([
            'court_cases.*',
            'cases.case_name',
            'cases.status as case_status',
            'creator.name as created_by_name',
            'updater.name as updated_by_name',

            // Offences
            DB::raw('GROUP_CONCAT(DISTINCT offences.offence_name SEPARATOR ", ") as offence_names'),
            DB::raw('GROUP_CONCAT(DISTINCT offence_categories.category_name SEPARATOR ", ") as category_names'),

            // Accused
            DB::raw('GROUP_CONCAT(DISTINCT CONCAT(accused.first_name, " ", accused.last_name) SEPARATOR ", ") as accused_names'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.address SEPARATOR ", ") as accused_addresses'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.contact SEPARATOR ", ") as accused_contacts'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.phone SEPARATOR ", ") as accused_phones'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.gender SEPARATOR ", ") as accused_genders'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.age SEPARATOR ", ") as accused_ages'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.date_of_birth SEPARATOR ", ") as accused_dob'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.island_id SEPARATOR ", ") as accused_islands'),

            // Victims
            DB::raw('GROUP_CONCAT(DISTINCT CONCAT(victims.first_name, " ", victims.last_name) SEPARATOR ", ") as victim_names'),
            DB::raw('GROUP_CONCAT(DISTINCT victims.address SEPARATOR ", ") as victim_addresses'),
            DB::raw('GROUP_CONCAT(DISTINCT victims.contact SEPARATOR ", ") as victim_contacts'),
            DB::raw('GROUP_CONCAT(DISTINCT victims.phone SEPARATOR ", ") as victim_phones'),
            DB::raw('GROUP_CONCAT(DISTINCT victims.gender SEPARATOR ", ") as victim_genders'),
            DB::raw('GROUP_CONCAT(DISTINCT victims.age SEPARATOR ", ") as victim_ages'),
            DB::raw('GROUP_CONCAT(DISTINCT victims.date_of_birth SEPARATOR ", ") as victim_dob'),
            DB::raw('GROUP_CONCAT(DISTINCT victims.island_id SEPARATOR ", ") as victim_islands'),
            DB::raw('GROUP_CONCAT(DISTINCT victims.age_group SEPARATOR ", ") as victim_age_groups'),
        ])
        ->where('court_cases.case_id', $caseId)
        ->when(!$onlyTrashed, function ($query) {
            return $query->whereNull('court_cases.deleted_at');
        })
        ->groupBy('court_cases.id')
        ->get();

    // Reset SQL mode
    DB::statement("SET SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");

    return $results;
}

    
}
