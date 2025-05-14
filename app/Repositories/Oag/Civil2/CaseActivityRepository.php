<?php

namespace App\Repositories\Oag\Civil2;

use App\Models\Oag\Civil2\CaseActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\CustomBaseRepository;

class CaseActivityRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CaseActivity::class;
    }

    /**
     * Create a new case activity record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return parent::create($data);
    }

    /**
     * Update an existing case activity record.
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
            throw new \Exception("Case activity not found.");
        }

        $model->update($data);
        return $model;
    }

    /**
     * Soft delete a case activity record.
     *
     * @param int $id
     * @return bool|null
     * @throws \Exception
     */
//     public function delete(int $id): bool
// {
//     $model = $this->getModelInstance()->find($id);

//     if (!$model) {
//         throw new \Exception("Cause of action not found.");
//     }

//     return $model->delete();
// }

    /**
     * Get all activities for a specific case.
     *
     * @param int $caseId
     * @param bool $includeTrashed
     * @return Collection
     */
    public function getByCaseId(int $caseId, bool $includeTrashed = false): Collection
    {
        $query = $this->getModelInstance()->where('case_id', $caseId)
            ->orderBy('activity_date', 'desc');

        if ($includeTrashed) {
            $query->withTrashed();
        }

        return $query->get();
    }

    /**
     * Get activities by a specific user.
     *
     * @param int $userId
     * @param bool $includeTrashed
     * @return Collection
     */
    public function getByUserId(int $userId, bool $includeTrashed = false): Collection
    {
        $query = $this->getModelInstance()->where('performed_by', $userId)
            ->orderBy('activity_date', 'desc');

        if ($includeTrashed) {
            $query->withTrashed();
        }

        return $query->get();
    }

    /**
     * Count total activities.
     *
     * @return int
     */
    public function count(): int
    {
        return parent::count();
    }
}
