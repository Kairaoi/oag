<?php

namespace App\Repositories\Oag\Civil2;

use App\Models\Oag\Civil2\CaseStatusHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\CustomBaseRepository;

class CaseStatusHistoryRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CaseStatusHistory::class;
    }

    /**
     * Create a new case status history record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return parent::create($data);
    }

    /**
     * Update an existing case status history record.
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
            throw new \Exception("Case status history not found.");
        }

        $model->update($data);
        return $model;
    }

    /**
     * Soft delete a case status history record.
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
     * Get all status history records for a specific case.
     *
     * @param int $caseId
     * @param bool $includeTrashed
     * @return Collection
     */
    public function getByCaseId(int $caseId, bool $includeTrashed = false): Collection
    {
        $query = $this->getModelInstance()->where('case_id', $caseId)
            ->orderBy('created_at', 'desc');

        if ($includeTrashed) {
            $query->withTrashed();
        }

        return $query->get();
    }

    /**
     * Count total history records.
     *
     * @return int
     */
    public function count(): int
    {
        return parent::count();
    }
}
