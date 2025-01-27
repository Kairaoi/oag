<?php

namespace App\Repositories\Oag\Draft;

use App\Models\OAG\Draft\Regulation;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\CustomBaseRepository;

class RegulationRepository extends CustomBaseRepository
{
    public function model()
    {
        return Regulation::class;
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
            ->leftJoin('ministries', 'regulations.ministry_id', '=', 'ministries.id')
            ->select('regulations.*', 'ministries.name as ministry_name');

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(status) LIKE ?', [$search]);
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
