<?php

namespace App\Repositories\Oag\Draft;

use App\Models\OAG\Draft\RegulationCounsel;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\CustomBaseRepository;

class RegulationCounselRepository extends CustomBaseRepository
{
    public function model()
    {
        return RegulationCounsel::class;
    }

    public function count(): int
    {
        return parent::count();
    }

    public function create(array $data): Model
    {
        return parent::create($data);
    }

    public function update(int $id, array $data): Model
    {
        return parent::update($id, $data);
    }

    public function getForDataTable($search = '', $order_by = '', $sort = 'asc', $trashed = false)
    {
        $dataTableQuery = $this->getModelInstance()->newQuery()
            ->leftJoin('regulations', 'regulation_counsel.regulation_id', '=', 'regulations.id')
            ->leftJoin('counsels', 'regulation_counsel.counsel_id', '=', 'counsels.id')
            ->select('regulation_counsel.*', 'regulations.name as regulation_name', 'counsels.name as counsel_name');

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(regulations.name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(counsels.name) LIKE ?', [$search]);
            });
        }

        if ($trashed) {
            $dataTableQuery->onlyTrashed();
        }

        if (!empty($order_by)) {
            $dataTableQuery->orderBy($order_by, $sort);
        }

        return $dataTableQuery->get();
    }

    public function pluck(): \Illuminate\Support\Collection
    {
        return $this->getModelInstance()->pluck('name', 'id');
    }
}
