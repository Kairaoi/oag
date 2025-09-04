<?php

namespace App\Repositories\Oag\Civil2;

use App\Models\Oag\Civil2\Civil2Case;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\CustomBaseRepository;
use DB;
class CaseRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return Civil2Case::class;
    }

    /**
     * Count the number of civil case records.
     *
     * @return int
     */
    public function count(): int
    {
        return parent::count();
    }

    /**
     * Create a new civil case record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return parent::create($data);
    }

    /**
     * Update an existing civil case.
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
            throw new \Exception("Model not found.");
        }

        $model->update($data);
        return $model;
    }

    /**
     * Get civil cases for datatable or listing view.
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @param bool $trashed
     * @return \Illuminate\Support\Collection
     */
   public function getForDataTable($search = '', $order_by = '', $sort = 'asc', $trashed = false): Collection
{
    $user = auth()->user();

    $query = $this->getModelInstance()->newQuery()
        ->join('users', 'civil2_cases.responsible_counsel_id', '=', 'users.id')
        ->join('court_categories', 'civil2_cases.court_type_id', '=', 'court_categories.id')
        ->join('causes_of_action', 'civil2_cases.cause_of_action_id', '=', 'causes_of_action.id')
        // Select most recent case_status by subquery or join (if only one allowed, use join)
        ->leftJoin('case_statuses', function ($join) {
            $join->on('civil2_cases.id', '=', 'case_statuses.case_id')
                 ->whereNull('case_statuses.deleted_at');
        })
        // ->leftJoin('case_pending_statuses', 'civil2_cases.case_pending_status_id', '=', 'case_pending_statuses.id')
        ->join('case_origin_types', 'civil2_cases.case_origin_type_id', '=', 'case_origin_types.id')

        // Join for plaintiff
        ->leftJoin('casse_counsels as cc_plaintiff', function ($join) {
            $join->on('civil2_cases.id', '=', 'cc_plaintiff.civil2_case_id')
                ->where('cc_plaintiff.role', 'plaintiff');
        })
        ->leftJoin('users as plaintiff_user', function ($join) {
            $join->on('cc_plaintiff.counsel_id', '=', 'plaintiff_user.id')
                ->where('cc_plaintiff.counsel_type', '=', \App\Models\User::class);
        })
        ->leftJoin('external_counsels as plaintiff_external', function ($join) {
            $join->on('cc_plaintiff.counsel_id', '=', 'plaintiff_external.id')
                ->where('cc_plaintiff.counsel_type', '=', \App\Models\Oag\Civil2\ExternalCounsel::class);
        })

        // Join for defendant
        ->leftJoin('casse_counsels as cc_defendant', function ($join) {
            $join->on('civil2_cases.id', '=', 'cc_defendant.civil2_case_id')
                ->where('cc_defendant.role', 'defendant');
        })
        ->leftJoin('users as defendant_user', function ($join) {
            $join->on('cc_defendant.counsel_id', '=', 'defendant_user.id')
                ->where('cc_defendant.counsel_type', '=', \App\Models\User::class);
        })
        ->leftJoin('external_counsels as defendant_external', function ($join) {
            $join->on('cc_defendant.counsel_id', '=', 'defendant_external.id')
                ->where('cc_defendant.counsel_type', '=', \App\Models\Oag\Civil2\ExternalCounsel::class);
        })

        ->select([
    'civil2_cases.id',
    'civil2_cases.case_file_no',
    'civil2_cases.court_case_no',
    'civil2_cases.case_name',
    'civil2_cases.date_received',
    'civil2_cases.date_opened',
    'civil2_cases.date_closed',

    'users.name as counsel_name',
    'court_categories.name as court_type',
    'causes_of_action.name as cause_of_action',

   DB::raw("CONCAT(DATE_FORMAT(case_statuses.status_date, '%Y-%m-%d'), ' - ', case_statuses.current_status) as status_and_case"),

   DB::raw("CONCAT(DATE_FORMAT(case_statuses.status_date, '%Y-%m-%d'), ' - ', case_statuses.action_required) as action_with_date"),

    'case_statuses.monitoring_status',

    'case_origin_types.name as origin_type',

    DB::raw("COALESCE(plaintiff_user.name, plaintiff_external.name) as plaintiff_name"),
    DB::raw("COALESCE(defendant_user.name, defendant_external.name) as defendant_name"),

    'civil2_cases.deleted_at',
        ]);


    if ($user->hasRole('civil.user')) {
        $query->where('civil2_cases.responsible_counsel_id', $user->id);
    }

    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%';
        $query->where(function ($q) use ($search) {
            $q->whereRaw('LOWER(civil2_cases.case_file_no) LIKE ?', [$search])
              ->orWhereRaw('LOWER(civil2_cases.case_name) LIKE ?', [$search])
              ->orWhereRaw('LOWER(civil2_cases.case_description) LIKE ?', [$search])
              ->orWhereRaw('LOWER(users.name) LIKE ?', [$search])
              ->orWhereRaw('LOWER(court_categories.name) LIKE ?', [$search])
              ->orWhereRaw('LOWER(causes_of_action.name) LIKE ?', [$search])
              ->orWhereRaw('LOWER(case_statuses.current_status) LIKE ?', [$search])
              ->orWhereRaw('LOWER(case_origin_types.name) LIKE ?', [$search]);
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
     * Pluck civil case names by ID.
     *
     * @return Collection
     */
    public function pluck(): Collection
    {
        return $this->getModelInstance()->pluck('case_name', 'id');
    }

    /**
     * Get active cases (not closed).
     *
     * @return array
     */
    public function getOpenCases(): array
    {
        return $this->model
            ->whereNull('date_closed')
            ->pluck('case_name', 'id')
            ->toArray();
    }
}
