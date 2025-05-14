<?php

namespace App\Repositories\Oag\Civil2;

use App\Models\Oag\Civil2\QuarterlyReport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\CustomBaseRepository;

class QuarterlyReportRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return QuarterlyReport::class;
    }

    /**
     * Create a new quarterly report record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return parent::create($data);
    }

    /**
     * Update an existing quarterly report record.
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
            throw new \Exception("Quarterly report not found.");
        }

        $model->update($data);
        return $model;
    }

    /**
     * Soft delete a quarterly report record.
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
     * Get quarterly reports for a specific counsel.
     *
     * @param int $counselId
     * @param bool $includeTrashed
     * @return Collection
     */
    public function getByCounselId(int $counselId, bool $includeTrashed = false): Collection
    {
        $query = $this->getModelInstance()->where('counsel_id', $counselId);

        if ($includeTrashed) {
            $query->withTrashed();
        }

        return $query->get();
    }

    /**
     * Get reports for a specific year and quarter.
     *
     * @param int $year
     * @param int $quarter
     * @return Collection
     */
    public function getByYearAndQuarter(int $year, int $quarter): Collection
    {
        return $this->getModelInstance()
                    ->where('year', $year)
                    ->where('quarter', $quarter)
                    ->get();
    }

    /**
     * Count total quarterly reports.
     *
     * @return int
     */
    public function count(): int
    {
        return parent::count();
    }
}
