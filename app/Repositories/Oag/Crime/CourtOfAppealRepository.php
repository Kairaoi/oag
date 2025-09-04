<?php

namespace App\Repositories\Oag\Crime;

use App\Models\Oag\Crime\CourtOfAppeal;
use App\Repositories\CustomBaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CourtOfAppealRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model()
    {
        return CourtOfAppeal::class;
    }

    /**
     * Create a new record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return parent::create($data);
    }

    /**
     * Update an existing record.
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        return parent::update($id, $data);
    }

    /**
     * Fetch all Court of Appeal records for a given case.
     *
     * @param int $caseId
     * @param bool $onlyTrashed
     * @return Collection
     */
    public function getCourtOfAppealByCaseId(int $caseId, bool $onlyTrashed = false): Collection
    {
        $query = $this->getModelInstance()
            ->leftJoin('cases', 'court_of_appeals.case_id', '=', 'cases.id')
            ->leftJoin('users as creator', 'court_of_appeals.created_by', '=', 'creator.id')
            ->leftJoin('users as updater', 'court_of_appeals.updated_by', '=', 'updater.id')
            ->select([
                'court_of_appeals.*',
                'cases.case_name',
                'creator.name as created_by_name',
                'updater.name as updated_by_name'
            ])
            ->where('court_of_appeals.case_id', $caseId);

        if ($onlyTrashed) {
            $query->onlyTrashed();
        }

        return $query->get();
    }

    /**
     * Get records for DataTable listing.
     *
     * @param string $search
     * @param string $orderBy
     * @param string $sort
     * @param bool $trashed
     * @return Collection
     */
    public function getForDataTable(string $search = '', string $orderBy = '', string $sort = 'asc', bool $trashed = false): Collection
    {
        $query = $this->getModelInstance()
            ->leftJoin('cases', 'court_of_appeals.case_id', '=', 'cases.id')
            ->leftJoin('users as creator', 'court_of_appeals.created_by', '=', 'creator.id')
            ->leftJoin('users as updater', 'court_of_appeals.updated_by', '=', 'updater.id')
            ->select([
                'court_of_appeals.*',
                'cases.case_name',
                'creator.name as created_by_name',
                'updater.name as updated_by_name'
            ]);

        if ($search) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(court_of_appeals.appeal_case_number) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(cases.case_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(creator.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(updater.name) LIKE ?', [$search]);
            });
        }

        if ($trashed) {
            $query->onlyTrashed();
        }

        if ($orderBy) {
            $validOrder = [
                'court_of_appeals.id',
                'appeal_case_number',
                'appeal_filing_date',
                'court_outcome',
                'judgment_delivered_date',
                'creator.name',
                'updater.name'
            ];

            if (in_array($orderBy, $validOrder)) {
                $query->orderBy($orderBy, $sort);
            }
        }

        return $query->get();
    }

    /**
     * Pluck appeal case numbers for dropdowns.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('appeal_case_number', 'id');
    }
}
