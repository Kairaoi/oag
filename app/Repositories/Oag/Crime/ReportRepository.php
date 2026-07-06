<?php

namespace App\Repositories\Oag\Crime;

use App\Models\OAG\Crime\Report;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    public function getAllReports()
    {
        return Report::all();
    }

    public function getReportById($reportId)
    {
        return Report::findOrFail($reportId);
    }

    public function executeReportQuery($query)
    {
        return \DB::select($query);
    }
}