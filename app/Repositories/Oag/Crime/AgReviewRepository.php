<?php

namespace App\Repositories\Oag\Crime;

use App\Models\OAG\Crime\AgReview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\CustomBaseRepository;
use Illuminate\Support\Facades\DB;

class AgReviewRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return AgReview::class;
    }

    /**
     * Create a new AG review submission.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return parent::create($data);
    }

    /**
     * Update a specific AG review (used to record the AG's decision).
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
            throw new \Exception("AG review not found.");
        }

        $model->update($data);
        return $model;
    }

    /**
     * Get AG reviews for DataTables listing.
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
            ->join('cases', 'ag_reviews.case_id', '=', 'cases.id')
            ->join('users as submitter', 'ag_reviews.submitted_by', '=', 'submitter.id')
            ->select([
                'ag_reviews.id',
                'ag_reviews.case_id',
                'ag_reviews.submitted_at',
                'ag_reviews.ag_decision',
                'ag_reviews.decision_date',
                'ag_reviews.ag_comments',
                'cases.case_name',
                'submitter.name as submitted_by_name',
                'ag_reviews.deleted_at',
            ]);

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(cases.case_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(ag_reviews.ag_decision) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(submitter.name) LIKE ?', [$search]);
            });
        }

        if ($trashed) {
            $query->withTrashed();
        }

        if (!empty($order_by)) {
            $query->orderBy($order_by, $sort);
        } else {
            $query->orderBy('ag_reviews.id', 'desc');
        }

        return $query->get();
    }

    /**
     * The latest (most recent) AG review submission for a case — since a
     * rejected submission can be revised and resubmitted, this is the row
     * that represents the case's *current* AG review state.
     */
    public function getLatestForCase(int $caseId): ?AgReview
    {
        return $this->getModelInstance()
            ->where('case_id', $caseId)
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Every other submission for this case, most recent first — lets the AG
     * see the outcome of earlier rounds (e.g. a prior rejection and their
     * own comments on it) when reviewing a resubmission.
     */
    public function getPriorSubmissions(int $caseId, int $excludeId): Collection
    {
        return $this->getModelInstance()
            ->where('case_id', $caseId)
            ->where('id', '!=', $excludeId)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Whether a case already has a submission awaiting decision or already
     * approved — used to block a duplicate "Submit to AG" while one is live.
     */
    public function hasActiveSubmission(int $caseId): bool
    {
        return $this->getModelInstance()
            ->where('case_id', $caseId)
            ->whereIn('ag_decision', ['pending', 'approved'])
            ->exists();
    }

    /**
     * Every submission for a case in chronological order — a case can be
     * submitted, rejected, revised and resubmitted multiple times, so this
     * is the full history rather than just the latest round.
     */
    public function getSubmissionsForCase(int $caseId): Collection
    {
        return $this->getModelInstance()
            ->where('case_id', $caseId)
            ->orderBy('id')
            ->get();
    }
}
