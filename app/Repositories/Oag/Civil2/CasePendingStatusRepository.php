<?php

namespace App\Repositories\Oag\Civil2;

use App\Models\Oag\Civil2\CasePendingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\CustomBaseRepository;

class CasePendingStatusRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CasePendingStatus::class;
    }

    /**
     * Create a new case pending status.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return parent::create($data);
    }

    /**
     * Update an existing case pending status.
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
            throw new \Exception("Case pending status not found.");
        }

        $model->update($data);
        return $model;
    }

    /**
     * Soft delete a case pending status.
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
     * Get all case pending statuses (with optional trashed).
     *
     * @param bool $trashed
     * @return Collection
     */
    public function getForDataTable(bool $trashed = false): Collection
    {
        $query = $this->getModelInstance()->newQuery();

        if ($trashed) {
            $query->withTrashed();
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Get data for dropdowns (id => name).
     *
     * @return Collection
     */
    public function pluck(): Collection
    {
        return $this->getModelInstance()->orderBy('name')->pluck('name', 'id');
    }

    /**
     * Count total records.
     *
     * @return int
     */
    public function count(): int
    {
        return parent::count();
    }
}
