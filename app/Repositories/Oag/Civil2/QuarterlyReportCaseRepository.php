<?php

namespace App\Repositories\Oag\Civil2;

use App\Models\Oag\Civil2\QuarterlyReportCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\CustomBaseRepository;

class QuarterlyReportCaseRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return QuarterlyReportCase::class;
    }

    /**
     * Create a new quarterly report case record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return parent::create($data);
    }

    /**
     * Update an existing quarterly report case record.
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
            throw new \Exception("Quarterly report case not found.");
        }

        $model->update($data);
        return $model;
    }

    /**
     * Soft delete a quarterly report case record.
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
     * Get quarterly report cases for a specific quarterly report.
     *
     * @param int $quarterlyReportId
     * @param bool $includeTrashed
     * @return Collection
     */
    public function getByQuarterlyReportId(int $quarterlyReportId, bool $includeTrashed = false): Collection
    {
        $query = $this->getModelInstance()->where('quarterly_report_id', $quarterlyReportId);

        if ($includeTrashed) {
            $query->withTrashed();
        }

        return $query->get();
    }

    /**
     * Get quarterly report cases for a specific case.
     *
     * @param int $caseId
     * @param bool $includeTrashed
     * @return Collection
     */
    public function getByCaseId(int $caseId, bool $includeTrashed = false): Collection
    {
        $query = $this->getModelInstance()->where('case_id', $caseId);

        if ($includeTrashed) {
            $query->withTrashed();
        }

        return $query->get();
    }

    /**
     * Count total quarterly report cases.
     *
     * @return int
     */
    public function count(): int
    {
        return parent::count();
    }
}
