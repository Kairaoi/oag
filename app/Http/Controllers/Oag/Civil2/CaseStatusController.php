<?php

namespace App\Http\Controllers\Oag\Civil2;

use App\Http\Controllers\Controller;

use App\Models\CivilCase;
use App\Models\CaseStatus;
use App\Models\CasePendingStatus;
use App\Models\CaseStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaseStatusController extends Controller
{
    /**
     * Show the form for updating a case status.
     *
     * @param  \App\Models\Case  $case
     * @return \Illuminate\Http\Response
     */
    public function edit(CivilCase $case)
    {
        $statuses = CaseStatus::all();
        $pendingStatuses = CasePendingStatus::all();
        
        return view('cases.status.edit', compact('case', 'statuses', 'pendingStatuses'));
    }

    /**
     * Update the status of the specified case.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Case  $case
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CivilCase $case)
    {
        $validated = $request->validate([
            'case_status_id' => 'required|exists:case_statuses,id',
            'case_pending_status_id' => 'nullable|exists:case_pending_statuses,id',
            'pending_with' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        // Check if status has actually changed
        $statusChanged = $case->case_status_id != $validated['case_status_id'] 
                      || $case->case_pending_status_id != $validated['case_pending_status_id']
                      || $case->pending_with != $validated['pending_with'];
        
        if ($statusChanged) {
            // Record status change in history
            CaseStatusHistory::create([
                'case_id' => $case->id,
                'case_status_id' => $validated['case_status_id'],
                'case_pending_status_id' => $validated['case_pending_status_id'],
                'pending_with' => $validated['pending_with'],
                'changed_by' => Auth::id(),
                'notes' => $validated['notes'] ?? 'Status updated'
            ]);
            
            // Update the case status
            $case->update([
                'case_status_id' => $validated['case_status_id'],
                'case_pending_status_id' => $validated['case_pending_status_id'],
                'pending_with' => $validated['pending_with'],
            ]);
            
            return redirect()->route('cases.show', $case)
                             ->with('success', 'Case status updated successfully');
        } else {
            return redirect()->route('cases.show', $case)
                             ->with('info', 'No changes made to case status');
        }
    }
    
    /**
     * Display the status history for a case.
     *
     * @param  \App\Models\Case  $case
     * @return \Illuminate\Http\Response
     */
    public function history(CivilCase $case)
    {
        $statusHistory = $case->statusHistory()
                             ->with(['status', 'pendingStatus', 'changedBy'])
                             ->orderBy('created_at', 'desc')
                             ->paginate(20);
        
        return view('cases.status.history', compact('case', 'statusHistory'));
    }
}