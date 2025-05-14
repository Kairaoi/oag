<?php

namespace App\Http\Controllers\Oag\Civil2;

use App\Http\Controllers\Controller;

use App\Models\Oag\Civil2\QuarterlyReport;
use App\Models\Oag\Civil2\QuarterlyReportCase;
use App\Models\Oag\Civil2\CivilCase;
use App\Models\Oag\Civil2\Counsel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class QuarterlyReportController extends Controller
{
    /**
     * Display a listing of the quarterly reports.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = QuarterlyReport::query();
        
        // Filter for my reports if requested
        if ($request->has('my_reports') && $request->my_reports) {
            $query->where('counsel_id', Auth::id());
        }
        
        // Filter by year if requested
        if ($request->has('year')) {
            $query->where('year', $request->year);
        }
        
        // Filter by quarter if requested
        if ($request->has('quarter')) {
            $query->where('quarter', $request->quarter);
        }
        
        $reports = $query->with('counsel')
                        ->orderBy('year', 'desc')
                        ->orderBy('quarter', 'desc')
                        ->paginate(15);
        
        // Get unique years for filter
        $years = QuarterlyReport::distinct('year')
                              ->orderBy('year', 'desc')
                              ->pluck('year');
        
        return view('quarterly-reports.index', compact('reports', 'years'));
    }

    /**
     * Show the form for creating a new quarterly report.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $counsels = Counsel::all();
        $currentYear = Carbon::now()->year;
        $currentQuarter = ceil(Carbon::now()->month / 3);
        
        return view('quarterly-reports.create', compact(
            'counsels', 
            'currentYear', 
            'currentQuarter'
        ));
    }

    /**
     * Store a newly created quarterly report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'counsel_id' => 'required|exists:counsels,id',
            'year' => 'required|integer|min:2000|max:' . (Carbon::now()->year + 1),
            'quarter' => 'required|integer|min:1|max:4',
            'notes' => 'nullable|string',
        ]);
        
        // Check if report already exists
        $existingReport = QuarterlyReport::where('counsel_id', $validated['counsel_id'])
                                      ->where('year', $validated['year'])
                                      ->where('quarter', $validated['quarter'])
                                      ->first();
        
        if ($existingReport) {
            return redirect()->route('quarterly-reports.edit', $existingReport)
                             ->with('info', 'A report for this quarter already exists. You can edit it below.');
        }
        
        // Create new report
        $report = QuarterlyReport::create($validated);
        
        return redirect()->route('quarterly-reports.edit', $report)
                         ->with('success', 'Quarterly report created successfully. Now you can add cases to it.');
    }

    /**
     * Display the specified quarterly report.
     *
     * @param  \App\Models\QuarterlyReport  $report
     * @return \Illuminate\Http\Response
     */
    public function show(QuarterlyReport $quarterlyReport)
    {
        $quarterlyReport->load([
            'counsel',
            'cases' => function ($query) {
                $query->orderBy('case_name');
            },
            'cases.counsel'
        ]);
        
        return view('quarterly-reports.show', compact('quarterlyReport'));
    }

    /**
     * Show the form for editing the specified quarterly report.
     *
     * @param  \App\Models\QuarterlyReport  $report
     * @return \Illuminate\Http\Response
     */
    public function edit(QuarterlyReport $quarterlyReport)
    {
        // Check if report is already submitted
        if ($quarterlyReport->submitted_at) {
            return redirect()->route('quarterly-reports.show', $quarterlyReport)
                             ->with('error', 'This report has already been submitted and cannot be edited.');
        }
        
        $quarterlyReport->load([
            'counsel',
            'cases' => function ($query) {
                $query->orderBy('case_name');
            }
        ]);
        
        // Get cases assigned to the counsel that are not already in the report
        $counselCases = CivilCase::where('counsel_id', $quarterlyReport->counsel_id)
                            ->whereNotIn('id', $quarterlyReport->cases->pluck('id'))
                            ->orderBy('case_name')
                            ->get();
        
        return view('quarterly-reports.edit', compact('quarterlyReport', 'counselCases'));
    }

    /**
     * Update the specified quarterly report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QuarterlyReport  $report
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QuarterlyReport $quarterlyReport)
    {
        // Check if report is already submitted
        if ($quarterlyReport->submitted_at) {
            return redirect()->route('quarterly-reports.show', $quarterlyReport)
                             ->with('error', 'This report has already been submitted and cannot be edited.');
        }
        
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);
        
        $quarterlyReport->update($validated);
        
        return redirect()->route('quarterly-reports.edit', $quarterlyReport)
                         ->with('success', 'Report updated successfully');
    }

    /**
     * Add a case to the quarterly report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QuarterlyReport  $report
     * @return \Illuminate\Http\Response
     */
    public function addCase(Request $request, QuarterlyReport $quarterlyReport)
    {
        // Check if report is already submitted
        if ($quarterlyReport->submitted_at) {
            return redirect()->route('quarterly-reports.show', $quarterlyReport)
                             ->with('error', 'This report has already been submitted and cannot be edited.');
        }
        
        $validated = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'current_status' => 'required|string',
            'required_work' => 'required|string',
            'other_counsel' => 'nullable|string',
        ]);
        
        // Check if case is already in the report
        $exists = $quarterlyReport->cases()->where('case_id', $validated['case_id'])->exists();
        
        if ($exists) {
            return redirect()->route('quarterly-reports.edit', $quarterlyReport)
                             ->with('error', 'This case is already in the report.');
        }
        
        // Add case to report
        $quarterlyReport->cases()->attach($validated['case_id'], [
            'current_status' => $validated['current_status'],
            'required_work' => $validated['required_work'],
            'other_counsel' => $validated['other_counsel'],
        ]);
        
        return redirect()->route('quarterly-reports.edit', $quarterlyReport)
                         ->with('success', 'Case added to report successfully');
    }

    /**
     * Update a case in the quarterly report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QuarterlyReport  $report
     * @param  \App\Models\Case  $case
     * @return \Illuminate\Http\Response
     */
    public function updateCase(Request $request, QuarterlyReport $quarterlyReport, CivilCase $case)
    {
        // Check if report is already submitted
        if ($quarterlyReport->submitted_at) {
            return redirect()->route('quarterly-reports.show', $quarterlyReport)
                             ->with('error', 'This report has already been submitted and cannot be edited.');
        }
        
        $validated = $request->validate([
            'current_status' => 'required|string',
            'required_work' => 'required|string',
            'other_counsel' => 'nullable|string',
        ]);
        
        // Update the pivot record
        $quarterlyReport->cases()->updateExistingPivot($case->id, $validated);
        
        return redirect()->route('quarterly-reports.edit', $quarterlyReport)
                         ->with('success', 'Case information updated successfully');
    }
    
    /**
     * Remove a case from the quarterly report.
     *
     * @param  \App\Models\QuarterlyReport  $report
     * @param  \App\Models\Case  $case
     * @return \Illuminate\Http\Response
     */
    public function removeCase(QuarterlyReport $quarterlyReport, CivilCase $case)
    {
        // Check if report is already submitted
        if ($quarterlyReport->submitted_at) {
            return redirect()->route('quarterly-reports.show', $quarterlyReport)
                             ->with('error', 'This report has already been submitted and cannot be edited.');
        }
        
        // Remove case from report
        $quarterlyReport->cases()->detach($case->id);
        
        return redirect()->route('quarterly-reports.edit', $quarterlyReport)
                         ->with('success', 'Case removed from report successfully');
    }
    
    /**
     * Submit the quarterly report.
     *
     * @param  \App\Models\QuarterlyReport  $report
     * @return \Illuminate\Http\Response
     */
    public function submit(QuarterlyReport $quarterlyReport)
    {
        // Check if report is already submitted
        if ($quarterlyReport->submitted_at) {
            return redirect()->route('quarterly-reports.show', $quarterlyReport)
                             ->with('error', 'This report has already been submitted.');
        }
        
        // Check if report has any cases
        if ($quarterlyReport->cases()->count() == 0) {
            return redirect()->route('quarterly-reports.edit', $quarterlyReport)
                             ->with('error', 'Cannot submit an empty report. Please add at least one case.');
        }
        
        // Submit the report
        $quarterlyReport->update([
            'submitted_at' => now(),
            'submitted_by' => Auth::id()
        ]);
        
        return redirect()->route('quarterly-reports.show', $quarterlyReport)
                         ->with('success', 'Quarterly report submitted successfully');
    }
    
    /**
     * Export the quarterly report to PDF.
     *
     * @param  \App\Models\QuarterlyReport  $report
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(QuarterlyReport $quarterlyReport)
    {
        $quarterlyReport->load([
            'counsel',
            'cases' => function ($query) {
                $query->orderBy('case_name');
            },
            'cases.counsel'
        ]);
        
        $pdf = PDF::loadView('quarterly-reports.pdf', compact('quarterlyReport'));
        $filename = "Quarterly_Report_{$quarterlyReport->year}_Q{$quarterlyReport->quarter}.pdf";
        
        return $pdf->download($filename);
    }

    /**
     * Remove the specified quarterly report from storage.
     *
     * @param  \App\Models\QuarterlyReport  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuarterlyReport $quarterlyReport)
    {
        // Check if user has permission
        $this->authorize('delete', $quarterlyReport);
        
        // Only allow deletion of unsubmitted reports
        if ($quarterlyReport->submitted_at) {
            return redirect()->route('quarterly-reports.index')
                             ->with('error', 'Submitted reports cannot be deleted.');
        }
        
        // Delete the report and its relationships
        DB::transaction(function () use ($quarterlyReport) {
            // Detach all cases
            $quarterlyReport->cases()->detach();
            
            // Delete the report
            $quarterlyReport->delete();
        });
        
        return redirect()->route('quarterly-reports.index')
                         ->with('success', 'Quarterly report deleted successfully');
    }
}