<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Crime\ReportRepository;

class ReportController extends Controller
{
    protected $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    // List all available reports
    public function index()
    {
        $reports = $this->reportRepository->getAllReports();
        return view('oag.crime.reports.index', compact('reports')); // ← Use index view for listing reports
    }

    // Show a specific report
    public function show($id)
{
    $report = $this->reportRepository->getReportById($id);
    $results = $this->reportRepository->executeReportQuery($report->query); // Get results from report query

    // Format data for Highcharts chart
    $chartData = $this->formatChartData($results);

    $columns = $results ? array_keys((array)$results[0]) : []; // Columns for table

    return view('oag.crime.reports.show', compact('report', 'results', 'columns', 'chartData'));
}

    

    // Format the data for Highcharts chart (generic function)
    private function formatChartData($results)
{
    $categories = [];
    $data = [];

    // Count victims by island_name
    $islandCounts = [];

    foreach ($results as $row) {
        // Check if island_name exists before accessing it
        $island = isset($row->island_name) ? $row->island_name : 'Unknown Island';
    
        // Increment the count for the island
        if (isset($islandCounts[$island])) {
            $islandCounts[$island]++;
        } else {
            $islandCounts[$island] = 1;
        }
    }
    

    // Prepare chart data
    foreach ($islandCounts as $island => $count) {
        $categories[] = $island;  // Island name as category
        $data[] = $count;         // Number of victims per island
    }

    return compact('categories', 'data');
}

    
}
