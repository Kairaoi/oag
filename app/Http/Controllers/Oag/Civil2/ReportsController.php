<?php

namespace App\Http\Controllers\Oag\Civil2;

use App\Http\Controllers\Controller;

use App\Models\Oag\Civil2\CivilCase;
use App\Models\Oag\Civil2\CaseStatus;
use App\Models\Oag\Civil2\CauseOfAction;
use App\Models\Oag\Civil2\Counsel;
use App\Models\Oag\Civil2\CaseOriginType;
use App\Models\Oag\Civil2\QuarterlyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display case status report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function caseStatus(Request $request)
    {
        // Get filters
        $yearFilter = $request->input('year', Carbon::now()->year);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Prepare query
        $query = CivilCase::query();
        
        // Apply filters
        if ($startDate) {
            $query->where('date_received', '>=', $startDate);
        } else {
            $query->whereYear('date_received', $yearFilter);
        }
        
        if ($endDate) {
            $query->where('date_received', '<=', $endDate);
        }
        
        // Get cases by status
        $casesByStatus = $query->select('case_statuses.name as status_name', DB::raw('count(*) as total'))
                              ->join('case_statuses', 'cases.case_status_id', '=', 'case_statuses.id')
                              ->groupBy('status_name')
                              ->get();
        
        // Get cases by month (for trend analysis)
        $casesByMonth = CivilCase::select(
                           DB::raw('YEAR(date_received) as year'),
                           DB::raw('MONTH(date_received) as month'),
                           DB::raw('count(*) as total')
                       )
                       ->whereYear('date_received', $yearFilter)
                       ->groupBy('year', 'month')
                       ->orderBy('year')
                       ->orderBy('month')
                       ->get();
        
        // Format data for charts
        $chartData = [
            'statusLabels' => $casesByStatus->pluck('status_name')->toArray(),
            'statusValues' => $casesByStatus->pluck('total')->toArray(),
            'monthLabels' => $casesByMonth->map(function($item) {
                return Carbon::createFromDate($item->year, $item->month, 1)->format('M Y');
            })->toArray(),
            'monthValues' => $casesByMonth->pluck('total')->toArray()
        ];
        
        // Get years for filter
        $years = CivilCase::selectRaw('DISTINCT YEAR(date_received) as year')
                   ->orderBy('year', 'desc')
                   ->pluck('year');
        
        return view('reports.case-status', compact('casesByStatus', 'casesByMonth', 'chartData', 'years', 'yearFilter', 'startDate', 'endDate'));
    }
    
    /**
     * Display counsel workload report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function counselWorkload(Request $request)
    {
        // Get currently active counsels
        $counsels = Counsel::all();
        
        // Get workload stats
        $workloadStats = [];
        
        foreach ($counsels as $counsel) {
            $totalCases = CivilCase::where('counsel_id', $counsel->id)->count();
            $openCases = CivilCase::where('counsel_id', $counsel->id)
                           ->whereHas('status', function($query) {
                               $query->where('name', 'OPEN');
                           })
                           ->count();
            $pendingCases = CivilCase::where('counsel_id', $counsel->id)
                              ->whereNotNull('case_pending_status_id')
                              ->count();
            $disposedCases = CivilCase::where('counsel_id', $counsel->id)
                               ->whereHas('status', function($query) {
                                   $query->where('name', 'DISPOSED-CLOSE');
                               })
                               ->count();
            
            $workloadStats[] = [
                'counsel' => $counsel,
                'total_cases' => $totalCases,
                'open_cases' => $openCases,
                'pending_cases' => $pendingCases,
                'disposed_cases' => $disposedCases
            ];
        }
        
        // Get quarterly report submission status
        $currentYear = Carbon::now()->year;
        $currentQuarter = ceil(Carbon::now()->month / 3);
        
        $quarterlyReportStatus = QuarterlyReport::where('year', $currentYear)
                                              ->where('quarter', $currentQuarter)
                                              ->get()
                                              ->keyBy('counsel_id');
        
        // Format data for charts
        $chartData = [
            'counselLabels' => collect($workloadStats)->pluck('counsel.name')->toArray(),
            'openCases' => collect($workloadStats)->pluck('open_cases')->toArray(),
            'pendingCases' => collect($workloadStats)->pluck('pending_cases')->toArray(),
            'disposedCases' => collect($workloadStats)->pluck('disposed_cases')->toArray()
        ];
        
        return view('reports.counsel-workload', compact('workloadStats', 'quarterlyReportStatus', 'chartData'));
    }
    
    /**
     * Display case types report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function caseTypes(Request $request)
    {
        // Get filters
        $yearFilter = $request->input('year', Carbon::now()->year);
        
        // Get cases by cause of action
        $casesByCauseOfAction = CivilCase::select('cause_of_action.name as cause_name', DB::raw('count(*) as total'))
                                   ->join('cause_of_action', 'cases.cause_of_action_id', '=', 'cause_of_action.id')
                                   ->whereYear('date_received', $yearFilter)
                                   ->groupBy('cause_name')
                                   ->orderBy('total', 'desc')
                                   ->get();
        
        // Get cases by origin type
        $casesByOriginType = CivilCase::select('case_origin_types.name as origin_name', DB::raw('count(*) as total'))
                                ->join('case_origin_types', 'cases.case_origin_type_id', '=', 'case_origin_types.id')
                                ->whereYear('date_received', $yearFilter)
                                ->groupBy('origin_name')
                                ->orderBy('total', 'desc')
                                ->get();
        
        // Get time to resolution stats
        $closedCases = CivilCase::select(
                            'cases.id',
                            'cases.date_received',
                            'case_closures.closure_date',
                            DB::raw('DATEDIFF(case_closures.closure_date, cases.date_received) as days_to_resolution')
                        )
                        ->join('case_closures', 'cases.id', '=', 'case_closures.case_id')
                        ->whereNotNull('case_closures.closure_date')
                        ->whereYear('case_closures.closure_date', $yearFilter)
                        ->get();
        
        $avgResolutionTime = $closedCases->avg('days_to_resolution');
        $maxResolutionTime = $closedCases->max('days_to_resolution');
        $minResolutionTime = $closedCases->min('days_to_resolution');
        
        // Format data for charts
        $chartData = [
            'causeLabels' => $casesByCauseOfAction->pluck('cause_name')->toArray(),
            'causeValues' => $casesByCauseOfAction->pluck('total')->toArray(),
            'originLabels' => $casesByOriginType->pluck('origin_name')->toArray(),
            'originValues' => $casesByOriginType->pluck('total')->toArray()
        ];
        
        // Get years for filter
        $years = CivilCase::selectRaw('DISTINCT YEAR(date_received) as year')
                   ->orderBy('year', 'desc')
                   ->pluck('year');
        
        return view('reports.case-types', compact(
            'casesByCauseOfAction', 
            'casesByOriginType', 
            'closedCases',
            'avgResolutionTime',
            'maxResolutionTime',
            'minResolutionTime',
            'chartData', 
            'years', 
            'yearFilter'
        ));
    }
    
    /**
     * Display custom report builder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customReport(Request $request)
    {
        // Get available filter options
        $counsels = Counsel::orderBy('name')->get();
        $statuses = CaseStatus::orderBy('name')->get();
        $causesOfAction = CauseOfAction::orderBy('name')->get();
        $originTypes = CaseOriginType::orderBy('name')->get();
        
        // Check if form is submitted
        $isSubmitted = $request->has('generate_report');
        $results = null;
        
        if ($isSubmitted) {
            // Build query based on filters
            $query = CivilCase::query();
            
            // Apply filters
            if ($request->filled('counsel_id')) {
                $query->where('counsel_id', $request->counsel_id);
            }
            
            if ($request->filled('case_status_id')) {
                $query->where('case_status_id', $request->case_status_id);
            }
            
            if ($request->filled('cause_of_action_id')) {
                $query->where('cause_of_action_id', $request->cause_of_action_id);
            }
            
            if ($request->filled('case_origin_type_id')) {
                $query->where('case_origin_type_id', $request->case_origin_type_id);
            }
            
            if ($request->filled('start_date')) {
                $query->where('date_received', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->where('date_received', '<=', $request->end_date);
            }
            
            // Get results with relationships
            $results = $query->with(['counsel', 'status', 'causeOfAction', 'originType'])
                           ->orderBy('date_received', 'desc')
                           ->paginate(50);
        }
        
        return view('reports.custom', compact(
            'counsels', 
            'statuses', 
            'causesOfAction', 
            'originTypes',
            'isSubmitted',
            'results'
        ));
    }
    
    /**
     * Export report to CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportCsv(Request $request)
    {
        // Build query based on filters
        $query = CivilCase::query();
        
        // Apply filters from request
        if ($request->filled('counsel_id')) {
            $query->where('counsel_id', $request->counsel_id);
        }
        
        if ($request->filled('case_status_id')) {
            $query->where('case_status_id', $request->case_status_id);
        }
        
        if ($request->filled('cause_of_action_id')) {
            $query->where('cause_of_action_id', $request->cause_of_action_id);
        }
        
        if ($request->filled('case_origin_type_id')) {
            $query->where('case_origin_type_id', $request->case_origin_type_id);
        }
        
        if ($request->filled('start_date')) {
            $query->where('date_received', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('date_received', '<=', $request->end_date);
        }
        
        // Get results with relationships
        $cases = $query->with(['counsel', 'status', 'causeOfAction', 'originType'])
                      ->orderBy('date_received', 'desc')
                      ->get();
        
        // Generate CSV filename
        $filename = 'case_report_' . date('Y-m-d') . '.csv';
        
        // Open output stream
        $handle = fopen('php://output', 'w');
        
        // Add CSV headers
        $headers = [
            'Case ID',
            'Case Name',
            'File Number',
            'Court Case Number',
            'Counsel',
            'Status',
            'Cause of Action',
            'Origin Type',
            'Date Received',
            'Description'
        ];
        
        // Set headers to download file
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Add headers to CSV
        fputcsv($handle, $headers);
        
        // Add data rows
        foreach ($cases as $case) {
            $row = [
                $case->id,
                $case->case_name,
                $case->file_number,
                $case->court_case_number,
                $case->counsel->name,
                $case->status->name,
                $case->causeOfAction->name,
                $case->originType->name,
                $case->date_received,
                $case->description
            ];
            
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        exit;
    }
}