<?php

namespace App\Repositories\Oag\Crime;

use App\Models\Oag\Crime\CaseReview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


class CaseReviewRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CaseReview::class;
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
        ->leftJoin('cases', 'case_reviews.case_id', '=', 'cases.id')
        ->leftJoin('users as creator', 'case_reviews.created_by', '=', 'creator.id')
        ->leftJoin('users as new_lawyer', 'case_reviews.new_lawyer_id', '=', 'new_lawyer.id')
        ->leftJoin('case_reallocations', function ($join) {
            $join->on('case_reviews.case_id', '=', 'case_reallocations.case_id');
        })
        ->leftJoin('users as from_lawyer', 'case_reallocations.from_lawyer_id', '=', 'from_lawyer.id')
        ->leftJoin('users as to_lawyer', 'case_reallocations.to_lawyer_id', '=', 'to_lawyer.id')
        ->select([
            'case_reviews.*',
            'cases.case_name',
            'creator.name as created_by_name',
            'new_lawyer.name as new_lawyer_name',
            'case_reallocations.reallocation_date',
            'case_reallocations.reallocation_reason as reallocation_details',
            'case_reallocations.created_by as reallocation_created_by',
            'case_reallocations.updated_by as reallocation_updated_by',
            'case_reallocations.created_at as reallocation_created_at',
            'case_reallocations.updated_at as reallocation_updated_at',
            'from_lawyer.name as from_lawyer_name',
            'to_lawyer.name as to_lawyer_name',
            // Include offence particulars directly from case_reviews
            'case_reviews.offence_particulars'
        ])
        ->distinct();

    // 🔐 Role-based access control
    $user = auth()->user();
    if ($user && $user->hasRole('cm.user')) {
        $dataTableQuery->where('cases.lawyer_id', $user->id); // Restrict to user's cases only
    }

    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%';
        $dataTableQuery->where(function ($query) use ($search) {
            $query->whereRaw('LOWER(case_reviews.review_notes) LIKE ?', [$search])
                ->orWhereRaw('LOWER(creator.name) LIKE ?', [$search])
                ->orWhereRaw('LOWER(cases.case_name) LIKE ?', [$search])
                ->orWhereRaw('LOWER(new_lawyer.name) LIKE ?', [$search])
                ->orWhereRaw('LOWER(from_lawyer.name) LIKE ?', [$search])
                ->orWhereRaw('LOWER(to_lawyer.name) LIKE ?', [$search])
                ->orWhereRaw('LOWER(case_reviews.evidence_status) LIKE ?', [$search])
                ->orWhereRaw('LOWER(case_reviews.action_type) LIKE ?', [$search])
                ->orWhereRaw('LOWER(case_reallocations.reallocation_reason) LIKE ?', [$search])
                // Add search filter for offence particulars
                ->orWhereRaw('LOWER(case_reviews.offence_particulars) LIKE ?', [$search]);
        });
    }

    if ($trashed) {
        $dataTableQuery->onlyTrashed();
    }

    if (!empty($order_by)) {
        $validOrderBy = [
            'id', 'case_id', 'created_by', 'review_notes', 'review_date',
            'evidence_status', 'action_type', 'created_by_name', 'case_name',
            'new_lawyer_name', 'from_lawyer_name', 'to_lawyer_name',
            'reallocation_date', 'reallocation_details',
            'reallocation_created_by', 'reallocation_updated_by',
            'reallocation_created_at', 'reallocation_updated_at',
            'case_reviews.offence_particulars' // Include offence particulars in the orderable columns
        ];

        if (in_array($order_by, $validOrderBy)) {
            $dataTableQuery->orderBy($order_by, $sort);
        }
    }

    return $dataTableQuery->get();
}

    

    /**
     * Pluck a list of values for a given column.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('review_notes', 'id');
    }

    /**
 * Get all reviewed cases for a given case ID, including offence and category details.
 *
 * @param int $caseId
 * @return \Illuminate\Support\Collection
 */


 public function getReviewsByCaseId(int $caseId, bool $onlyTrashed = false): \Illuminate\Support\Collection
{
    // Get the model's table columns to use in GROUP BY
    $caseReviewColumns = Schema::getColumnListing('case_reviews');

    $query = $this->getModelInstance();

    if ($onlyTrashed) {
        $query = $query->onlyTrashed();
    }

    $result = $query
        ->leftJoin('cases', 'case_reviews.case_id', '=', 'cases.id')
        ->leftJoin('users as creator', 'case_reviews.created_by', '=', 'creator.id')
        ->leftJoin('users as new_lawyer', 'case_reviews.new_lawyer_id', '=', 'new_lawyer.id')
        ->leftJoin('case_reallocations', 'case_reviews.case_id', '=', 'case_reallocations.case_id')
        ->leftJoin('users as from_lawyer', 'case_reallocations.from_lawyer_id', '=', 'from_lawyer.id')
        ->leftJoin('users as to_lawyer', 'case_reallocations.to_lawyer_id', '=', 'to_lawyer.id')
        ->leftJoin('case_offence', 'case_reviews.case_id', '=', 'case_offence.case_id')
        ->leftJoin('offences', 'case_offence.offence_id', '=', 'offences.id')
        ->leftJoin('offence_categories', 'offences.offence_category_id', '=', 'offence_categories.id')

        ->leftJoin('accused', 'case_reviews.case_id', '=', 'accused.case_id')
        ->leftJoin('victims', 'case_reviews.case_id', '=', 'victims.case_id')
        ->select([
            'case_reviews.*',
            'cases.case_name',
            'creator.name as created_by_name',
            'new_lawyer.name as new_lawyer_name',
            'case_reallocations.reallocation_date',
            'case_reallocations.reallocation_reason as reallocation_details',
            'case_reallocations.created_by as reallocation_created_by',
            'case_reallocations.updated_by as reallocation_updated_by',
            'case_reallocations.created_at as reallocation_created_at',
            'case_reallocations.updated_at as reallocation_updated_at',
            'from_lawyer.name as from_lawyer_name',
            'to_lawyer.name as to_lawyer_name',
            'case_reviews.offence_particulars',
            DB::raw('GROUP_CONCAT(DISTINCT offences.offence_name SEPARATOR ", ") as offence_names'),
            DB::raw('GROUP_CONCAT(DISTINCT offence_categories.category_name SEPARATOR ", ") as category_names'),

            // Accused particulars
            DB::raw('GROUP_CONCAT(DISTINCT CONCAT(accused.first_name, " ", accused.last_name) SEPARATOR ", ") as accused_names'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.address SEPARATOR ", ") as accused_addresses'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.contact SEPARATOR ", ") as accused_contacts'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.phone SEPARATOR ", ") as accused_phones'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.gender SEPARATOR ", ") as accused_genders'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.age SEPARATOR ", ") as accused_ages'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.date_of_birth SEPARATOR ", ") as accused_dob'),
            DB::raw('GROUP_CONCAT(DISTINCT accused.island_id SEPARATOR ", ") as accused_islands'),

            // Victim particulars
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
        ->where('case_reviews.case_id', $caseId)
        ->when(!$onlyTrashed, function ($query) {
            return $query->whereNull('case_reviews.deleted_at');
        });

    // Disable ONLY_FULL_GROUP_BY for this session
    DB::statement("SET SQL_MODE=''");

    $result = $result->groupBy('case_reviews.id')->get();

    // Reset SQL_MODE
    DB::statement("SET SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");

    return $result;
}


}
