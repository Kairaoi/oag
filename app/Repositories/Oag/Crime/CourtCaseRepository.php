<?php

namespace App\Repositories\Oag\Crime;

use App\Models\OAG\Crime\CourtCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\CustomBaseRepository;

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
}
