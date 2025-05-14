<?php

namespace App\Http\Controllers\Oag\Civil2;

use App\Http\Controllers\Controller;

use App\Models\Oag\Civil2\CivilCase;
use App\Models\Oag\Civil2\CaseActivity;
use App\Models\Oag\Civil2\CaseStatus;
use App\Models\Oag\Civil2\QuarterlyReport;
use App\Models\Oag\Civil2\Counsel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get current user's role
        $user = Auth::user();
        $isAdmin = $user->hasRole('Administrator');
        $isSeniorCounsel = $user->hasRole('Senior Counsel');
        
        // Statistics for all users
        $dashboardData = [
            'my_cases_count' => CivilCase::where('counsel_id', Auth::id())->count(),
            'my_open_cases_count' => CivilCase::where('counsel_id', Auth::id())
                                      ->whereHas('status', function($query) {
                                          $query->where('name', 'OPEN');
                                      })
                                      ->count(),
            'my_pending_cases_count' => CivilCase::where('counsel_id', Auth::id())
                                       ->whereNotNull('case_pending_status_id')
                                       ->count(),
            'my_recent_activities' => CaseActivity::whereHas('case', function($query) {
                                         $query->where('counsel_id', Auth::id());
                                       })
                                       ->orderBy('created_at', 'desc')
                                       ->take(5)
                                       ->get(),
            'my_upcoming_activities' => CaseActivity::whereHas('case', function($query) {
                                          $query->where('counsel_id', Auth::id());
                                        })
                                        ->where('activity_date', '>=', Carbon::now())
                                        ->orderBy('activity_date', 'asc')
                                        ->take(5)
                                        ->get(),
            'quarterly_report_status' => $this->getQuarterlyReportStatus()
        ];
        
        // Additional statistics for administrators and senior counsel
        if ($isAdmin || $isSeniorCounsel) {
            // Get case status distribution
            $caseStatusDistribution = DB::table('cases')
                                    ->select('case_statuses.name', DB::raw('count(*) as total'))
                                    ->join('case_statuses', 'cases.case_status_id', '=', 'case_statuses.id')
                                    ->groupBy('case_statuses.name')
                                    ->get();
            
            // Get counsel workload
            $counselWorkload = DB::table('cases')
                              ->select('counsels.name', DB::raw('count(*) as case_count'))
                              ->join('counsels', 'cases.counsel_id', '=', 'counsels.id')
                              ->whereHas('status', function($query) {
                                  $query->where('name', 'OPEN');
                              })
                              ->groupBy('counsels.name')
                              ->get();
            
            // Get recent cases
            $recentCases = CivilCase::with(['counsel', 'status'])
                             ->orderBy('created_at', 'desc')
                             ->take(10)
                             ->get();
            
            // Get quarterly report submission stats
            $currentYear = Carbon::now()->year;
            $currentQuarter = ceil(Carbon::now()->month / 3);
            
            $quarterlyReportStats = DB::table('quarterly_reports')
                                  ->select(
                                      'counsel_id',
                                      'counsels.name as counsel_name',
                                      DB::raw('CASE WHEN submitted_at IS NOT NULL THEN true ELSE false END as is_submitted')
                                  )
                                  ->leftJoin('counsels', 'quarterly_reports.counsel_id', '=', 'counsels.id')
                                  ->where('year', $currentYear)
                                  ->where('quarter', $currentQuarter)
                                  ->get()
                                  ->groupBy('is_submitted');
            
            $dashboardData = array_merge($dashboardData, [
                'total_cases_count' => CivilCase::count(),
                'open_cases_count' => CivilCase::whereHas('status', function($query) {
                                          $query->where('name', 'OPEN');
                                      })->count(),
                'pending_cases_count' => CivilCase::whereNotNull('case_pending_status_id')->count(),
                'disposed_cases_count' => CivilCase::whereHas('status', function($query) {
                                             $query->where('name', 'DISPOSED-CLOSE');
                                         })->count(),
                'case_status_distribution' => $caseStatusDistribution,
                'counsel_workload' => $counselWorkload,
                'recent_cases' => $recentCases,
                'quarterly_report_stats' => $quarterlyReportStats
            ]);
        }
        
        return view('dashboard', compact('dashboardData'));
    }
    
    /**
     * Get the quarterly report status for the current user.
     *
     * @return array
     */
    private function getQuarterlyReportStatus()
    {
        $currentYear = Carbon::now()->year;
        $currentQuarter = ceil(Carbon::now()->month / 3);
        
        $report = QuarterlyReport::where('counsel_id', Auth::id())
                                ->where('year', $currentYear)
                                ->where('quarter', $currentQuarter)
                                ->first();
        
        if (!$report) {
            return [
                'status' => 'not_created',
                'message' => 'Your quarterly report for Q'.$currentQuarter.' '.$currentYear.' has not been created yet.',
                'report' => null
            ];
        } elseif (!$report->submitted_at) {
            return [
                'status' => 'in_progress',
                'message' => 'Your quarterly report for Q'.$currentQuarter.' '.$currentYear.' is in progress.',
                'report' => $report
            ];
        } else {
            return [
                'status' => 'submitted',
                'message' => 'Your quarterly report for Q'.$currentQuarter.' '.$currentYear.' has been submitted on '.Carbon::parse($report->submitted_at)->format('M d, Y'),
                'report' => $report
            ];
        }
    }
}