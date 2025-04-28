<?php

namespace App\Repositories;


use App\Models\Oag\Crime\Report;

use Illuminate\Support\Facades\DB;

class ReportRepository
{
    public function getAllReports()
    {
        return Report::with('reportGroup')->get();
    }

    public function getReportById($id)
    {
        return Report::findOrFail($id);
    }

    public function executeReport(Report $report)
    {
        // Optional: You can add parameter support here later
        return DB::select(DB::raw($report->query));
    }
}
