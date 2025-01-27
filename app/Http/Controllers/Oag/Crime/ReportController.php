<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Oag\Crime\ReportRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{

    protected $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
       
        $this->reportRepository = $reportRepository;
    }

    public function index()
    {
        // Fetch all reports from the ReportRepository
        $reports = $this->reportRepository->getAllReports();
        // Return a view with the fetched reports data
        return view('oag.reports.index', compact('reports'));
    }

    public function executeReport(Request $request, $reportId)
    {
        // Fetch the report by ID from the ReportRepository
        $report = $this->reportRepository->getReportById($reportId);
    
        // Execute the report query using the ReportRepository
        $results = $this->reportRepository->executeReportQuery($report->query);
        
        // Check if $results is an instance of Illuminate\Contracts\Support\Arrayable
        if ($results instanceof Arrayable) {
            // If $results is Arrayable, convert it to an array
            $results = $results->toArray();
        } elseif (!is_array($results)) {
            // If $results is not an array or Arrayable, handle the error
            return redirect()->back()->with('error', 'Invalid report data format');
        }
    
        // Pass the data to the view
        return view('oag.reports.result', compact('results'));
    }

    // Helper function to check if the array contains data suitable for Highcharts graph
    private function isValidChartData($array)
    {
        // Implement your logic to check if the array contains valid data for Highcharts
        // For example, check if the array contains 'category' and 'value' keys
        return isset($array[0]['category']) && isset($array[0]['value']);
    }

    public function showResults($reportId)
    {
        // Fetch the report by ID from the ReportRepository
        $report = $this->reportRepository->getReportById($reportId);

        // Check if the report exists
        if ($report) {
            // Execute the report query using the ReportRepository
            $results = $this->reportRepository->executeReportQuery($report->query);

            // Check if $results is an instance of Illuminate\Support\Collection
            if ($results instanceof Collection) {
                // If $results is a Collection, pass it directly to the view
                return view('oag.report.result', compact('results'));
            } elseif (is_array($results)) {
                // If $results is an array, pass it directly to the view
                return view('oag.report.result', compact('results'));
            } elseif (is_string($results)) {
                // If $results is a JSON string, decode it to an array and pass it to the view
                $results = json_decode($results, true); // Decode JSON to an associative array
                return view('oag.report.result', compact('results'));
            } else {
                // If $results is not a Collection, array, or JSON string, handle the error
                return redirect()->back()->with('error', 'Invalid report data format');
            }
        } else {
            // If the report is not found, handle the error
            return redirect()->back()->with('error', 'Report not found');
        }
    }

    public function pimsDashboard(Request $request, $reportId)
    {
        // Fetch the report by ID from the ReportRepository
        $report = $this->reportRepository->getReportById($reportId);

        // Check if the report exists
        if (!$report) {
            // If the report is not found, handle the error
            return redirect()->back()->with('error', 'Report not found');
        }

        // Execute the report query using the ReportRepository
        $results = $this->reportRepository->executeReportQuery($report->query);

        // Pass the data to the view along with the $reportId
        return view('oag.reports.dashboard', compact('report', 'results', 'reportId'));
    }

    public function activity(Request $request, $activityId)
    {
        // Fetch the activity by ID from your repository or model
        $activity = Activity::findOrFail($activityId);

        // Fetch and execute the report query using your ReportRepository
        $results = $this->reportRepository->executeReportQuery($activity->report_query);

        // Pass the data to the view
        return view('oag.reports.dashboard', compact('activity', 'results'));
    }
}
