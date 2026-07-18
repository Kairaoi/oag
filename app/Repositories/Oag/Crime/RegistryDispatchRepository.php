<?php

namespace App\Repositories\Oag\Crime;

use App\Models\OAG\Crime\RegistryDispatch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\CustomBaseRepository;

class RegistryDispatchRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return RegistryDispatch::class;
    }

    /**
     * Create a new dispatch record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return parent::create($data);
    }

    /**
     * Update a specific dispatch record.
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
            throw new \Exception("Registry dispatch not found.");
        }

        $model->update($data);
        return $model;
    }

    /**
     * Get dispatch records for DataTables listing.
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
            ->join('cases', 'registry_dispatches.case_id', '=', 'cases.id')
            ->join('users as dispatcher', 'registry_dispatches.dispatched_by', '=', 'dispatcher.id')
            ->select([
                'registry_dispatches.id',
                'registry_dispatches.case_id',
                'registry_dispatches.date_dispatched',
                'registry_dispatches.dispatched_to',
                'cases.case_name',
                'dispatcher.name as dispatched_by_name',
                'registry_dispatches.deleted_at',
            ]);

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(cases.case_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(registry_dispatches.dispatched_to) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(dispatcher.name) LIKE ?', [$search]);
            });
        }

        if ($trashed) {
            $query->withTrashed();
        }

        if (!empty($order_by)) {
            $query->orderBy($order_by, $sort);
        } else {
            $query->orderBy('registry_dispatches.id', 'desc');
        }

        return $query->get();
    }

    /**
     * Whether a case has already been dispatched.
     */
    public function hasDispatch(int $caseId): bool
    {
        return $this->getModelInstance()->where('case_id', $caseId)->exists();
    }

    /**
     * Dispatch record(s) for a case, in chronological order.
     */
    public function getByCaseId(int $caseId): Collection
    {
        return $this->getModelInstance()
            ->where('case_id', $caseId)
            ->orderBy('id')
            ->get();
    }
}
