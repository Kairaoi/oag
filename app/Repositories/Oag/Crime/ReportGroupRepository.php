<?php
namespace App\Repositories\Oag\Crime;

use App\Repositories\CustomBaseRepository;
use App\Models\Oag\Crime\Report;
use App\Models\Oag\Crime\ReportGroup;

class ReportGroupRepository extends CustomBaseRepository
{
    /**
     * Specify the model class name
     *
     * @return string
     */
    public function model()
    {
        return ReportGroup::class;
    }

    /**
     * Get a listing of ReportGroups filtered by the module
     *
     * @param string $module
     * @return \Illuminate\Support\Collection
     */
    public function listing($module)
    {
        return $this->getModelInstance()
            ->where('module', '=', $module)
            ->orderBy('sort_order')
            ->distinct()
            ->get()
            ->pluck('name', 'id');
    }
}
