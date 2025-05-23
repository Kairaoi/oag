<?php

namespace App\Repositories\Oag\Crime;

use App\Models\Oag\Crime\AppealDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;



class AppealDetailRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return AppealDetail::class;
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
     * @return Collection
     */
    public function getForDataTable($search = '', $order_by = '', $sort = 'asc', $trashed = false): Collection
    {
        $query = $this->getModelInstance()->newQuery()
            ->leftJoin('cases', 'appeal_details.case_id', '=', 'cases.id')
            ->leftJoin('court_cases', 'appeal_details.court_case_id', '=', 'court_cases.id')
            ->leftJoin('users as creator', 'appeal_details.created_by', '=', 'creator.id')
            ->leftJoin('users as updater', 'appeal_details.updated_by', '=', 'updater.id')
            ->select([
                'appeal_details.*',
                'cases.case_name',
                'court_cases.court_case_number',
                'creator.name as created_by_name',
                'updater.name as updated_by_name',
            ]);

        if ($search) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(appeal_details.appeal_case_number) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(appeal_details.appeal_grounds) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(appeal_details.appeal_decision) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(creator.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(updater.name) LIKE ?', [$search]);
            });
        }

        if ($trashed) {
            $query->onlyTrashed();
        }

        if ($order_by) {
            $validOrderBy = [
                'appeal_details.id',
                'appeal_case_number',
                'appeal_filing_date',
                'appeal_status',
                'appeal_decision_date',
                'cases.case_name',
                'court_cases.court_case_number',
                'creator.name',
                'updater.name',
            ];

            if (in_array($order_by, $validOrderBy)) {
                $query->orderBy($order_by, $sort);
            }
        }

        return $query->get();
    }

    /**
     * Get all appeal details for a specific case.
     *
     * @param int $caseId
     * @param bool $onlyTrashed
     * @return \Illuminate\Support\Collection
     */
    public function getAppealsByCaseId(int $caseId, bool $onlyTrashed = false): \Illuminate\Support\Collection
    {
        $query = $this->getModelInstance()
            ->leftJoin('court_cases', 'appeal_details.court_case_id', '=', 'court_cases.id')
            ->leftJoin('users as creator', 'appeal_details.created_by', '=', 'creator.id')
            ->leftJoin('users as updater', 'appeal_details.updated_by', '=', 'updater.id')
            ->select([
                'appeal_details.*',
                'court_cases.court_case_number',
                'creator.name as created_by_name',
                'updater.name as updated_by_name',
            ])
            ->where('appeal_details.case_id', $caseId);

        if ($onlyTrashed) {
            $query->onlyTrashed();
        }

        return $query->get();
    }

    /**
     * Pluck appeal case numbers for dropdowns or selectors.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('appeal_case_number', 'id');
    }

    public function getAppealDetailsByCaseId(int $caseId, bool $onlyTrashed = false): \Illuminate\Support\Collection
    {
        $query = $this->getModelInstance()->newQuery();
    
        if ($onlyTrashed) {
            $query->onlyTrashed();
        } else {
            $query->whereNull('appeal_details.deleted_at');
        }
    
        return $query
            ->leftJoin('cases', 'appeal_details.case_id', '=', 'cases.id')
            ->leftJoin('users as creator', 'appeal_details.created_by', '=', 'creator.id')
            ->leftJoin('users as updater', 'appeal_details.updated_by', '=', 'updater.id')
            ->leftJoin('case_offence', 'appeal_details.case_id', '=', 'case_offence.case_id')
            ->leftJoin('offences', 'case_offence.offence_id', '=', 'offences.id')
            ->leftJoin('offence_categories', 'offences.offence_category_id', '=', 'offence_categories.id')
            ->leftJoin('accused', 'appeal_details.case_id', '=', 'accused.case_id')
            ->leftJoin('victims', 'appeal_details.case_id', '=', 'victims.case_id')
            ->where('appeal_details.case_id', $caseId)
            ->selectRaw('
                appeal_details.id,
                appeal_details.case_id,
                appeal_details.created_by,
                appeal_details.updated_by,
                appeal_details.created_at,
                appeal_details.updated_at,
                appeal_details.deleted_at,
                appeal_details.filing_date_source,
                appeal_details.appeal_filing_date,
                cases.case_name, 
                cases.status as case_status, 
                creator.name as created_by_name, 
                updater.name as updated_by_name, 
                appeal_details.appeal_filing_date as appeal_filing_effective_date, 
                CASE 
                    WHEN appeal_details.filing_date_source = "court" THEN "court"
                    WHEN appeal_details.filing_date_source = "defendant" THEN "defendant"
                    ELSE NULL
                END as filing_date_origin,
                GROUP_CONCAT(DISTINCT offences.offence_name SEPARATOR ", ") as offence_names, 
                GROUP_CONCAT(DISTINCT offence_categories.category_name SEPARATOR ", ") as category_names, 
                GROUP_CONCAT(DISTINCT CONCAT(accused.first_name, " ", accused.last_name) SEPARATOR ", ") as accused_names, 
                GROUP_CONCAT(DISTINCT accused.address SEPARATOR ", ") as accused_addresses, 
                GROUP_CONCAT(DISTINCT accused.contact SEPARATOR ", ") as accused_contacts, 
                GROUP_CONCAT(DISTINCT accused.phone SEPARATOR ", ") as accused_phones, 
                GROUP_CONCAT(DISTINCT accused.gender SEPARATOR ", ") as accused_genders, 
                GROUP_CONCAT(DISTINCT accused.age SEPARATOR ", ") as accused_ages, 
                GROUP_CONCAT(DISTINCT accused.date_of_birth SEPARATOR ", ") as accused_dob, 
                GROUP_CONCAT(DISTINCT accused.island_id SEPARATOR ", ") as accused_islands, 
                GROUP_CONCAT(DISTINCT CONCAT(victims.first_name, " ", victims.last_name) SEPARATOR ", ") as victim_names, 
                GROUP_CONCAT(DISTINCT victims.address SEPARATOR ", ") as victim_addresses, 
                GROUP_CONCAT(DISTINCT victims.contact SEPARATOR ", ") as victim_contacts, 
                GROUP_CONCAT(DISTINCT victims.phone SEPARATOR ", ") as victim_phones, 
                GROUP_CONCAT(DISTINCT victims.gender SEPARATOR ", ") as victim_genders, 
                GROUP_CONCAT(DISTINCT victims.age SEPARATOR ", ") as victim_ages, 
                GROUP_CONCAT(DISTINCT victims.date_of_birth SEPARATOR ", ") as victim_dob, 
                GROUP_CONCAT(DISTINCT victims.island_id SEPARATOR ", ") as victim_islands, 
                GROUP_CONCAT(DISTINCT victims.age_group SEPARATOR ", ") as victim_age_groups
            ')
            ->groupBy(
                'appeal_details.id',
                'appeal_details.case_id',
                'appeal_details.created_by',
                'appeal_details.updated_by',
                'appeal_details.created_at',
                'appeal_details.updated_at',
                'appeal_details.deleted_at',
                'appeal_details.filing_date_source',
                'appeal_details.appeal_filing_date',
                'cases.case_name',
                'cases.status',
                'creator.name',
                'updater.name'
            )
            ->get();
    }

    
}
