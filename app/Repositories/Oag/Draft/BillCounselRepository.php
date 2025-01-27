<?php

namespace App\Repositories\Oag\Draft;

use App\Models\OAG\Draft\BillCounsel;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\CustomBaseRepository;

class BillCounselRepository extends CustomBaseRepository
{
    public function model()
    {
        return BillCounsel::class;
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
            ->leftJoin('bills', 'bill_counsel.bill_id', '=', 'bills.id')
            ->leftJoin('counsels', 'bill_counsel.counsel_id', '=', 'counsels.id')
            ->select('bill_counsel.*', 'bills.name as bill_name', 'counsels.name as counsel_name');

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(bills.name) LIKE ?', [$search])
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
