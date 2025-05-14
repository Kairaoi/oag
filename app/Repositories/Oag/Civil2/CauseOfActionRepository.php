<?php

namespace App\Repositories\Oag\Civil2;

use App\Models\Oag\Civil2\CauseOfAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\CustomBaseRepository;

class CauseOfActionRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CauseOfAction::class;
    }

    /**
     * Create a new cause of action.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return parent::create($data);
    }

    /**
     * Update an existing cause of action.
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
            throw new \Exception("Cause of action not found.");
        }

        $model->update($data);
        return $model;
    }

    /**
     * Soft delete a cause of action.
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
     * Get all causes of action (with optional trashed).
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
