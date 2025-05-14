<?php

namespace App\Repositories\Oag\Civil2;

use App\Models\Oag\Civil2\Civil2Case;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\CustomBaseRepository;

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
            ->join('case_statuses', 'civil2_cases.case_status_id', '=', 'case_statuses.id')
            ->leftJoin('case_pending_statuses', 'civil2_cases.case_pending_status_id', '=', 'case_pending_statuses.id')
            ->join('case_origin_types', 'civil2_cases.case_origin_type_id', '=', 'case_origin_types.id')
            ->select([
                'civil2_cases.id',
                'civil2_cases.case_file_no',
                'civil2_cases.court_case_no',
                'civil2_cases.case_name',
                'civil2_cases.date_received',
                'civil2_cases.date_opened',
                'civil2_cases.date_closed',
                'civil2_cases.case_description',
                'users.name as counsel_name',
                'court_categories.name as court_type',
                'causes_of_action.name as cause_of_action',
                'case_statuses.name as case_status',
                'case_pending_statuses.name as pending_status',
                'case_origin_types.name as origin_type',
                'civil2_cases.deleted_at',
            ]);

        // Optional role-based filtering (customize as needed)
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
                  ->orWhereRaw('LOWER(case_statuses.name) LIKE ?', [$search])
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
