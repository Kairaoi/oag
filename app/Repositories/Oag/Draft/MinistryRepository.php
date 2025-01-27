<?php

namespace App\Repositories\Oag\Draft;

use App\Models\OAG\Draft\Ministry; // Update namespace accordingly
use Illuminate\Database\Eloquent\Model;
use App\Repositories\CustomBaseRepository;

class MinistryRepository extends CustomBaseRepository
{
    public function model()
    {
        return Ministry::class; 
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
        $dataTableQuery = $this->getModelInstance()->newQuery();

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $dataTableQuery->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(code) LIKE ?', [$search]);
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
