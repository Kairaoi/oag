<?php

namespace App\Http\Controllers\Oag\Civil2;

use App\Http\Controllers\Controller;

use App\Models\Oag\Civil2\CivilCase;
use App\Models\Oag\Civil2\CaseActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CaseActivityController extends Controller
{
    /**
     * Display a listing of the case activities.
     *
     * @param  \App\Models\Case  $case
     * @return \Illuminate\Http\Response
     */
    public function index(CivilCase $case)
    {
        $activities = $case->activities()
                          ->orderBy('activity_date', 'desc')
                          ->paginate(15);
                          
        return view('cases.activities.index', compact('case', 'activities'));
    }

    /**
     * Show the form for creating a new case activity.
     *
     * @param  \App\Models\Case  $case
     * @return \Illuminate\Http\Response
     */
    public function create(CivilCase $case)
    {
        return view('cases.activities.create', compact('case'));
    }

    /**
     * Store a newly created case activity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Case  $case
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, CivilCase $case)
    {
        $validated = $request->validate([
            'activity_type' => 'required|string|max:50',
            'activity_date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document' => 'nullable|file|max:10240', // 10MB max file size
        ]);
        
        $activity = new CaseActivity($validated);
        $activity->case_id = $case->id;
        $activity->created_by = Auth::id();
        
        // Handle document upload if provided
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('case_documents/' . $case->id);
            $activity->document_path = $path;
            $activity->document_name = $request->file('document')->getClientOriginalName();
        }
        
        $activity->save();
        
        return redirect()->route('cases.activities.index', $case)
                         ->with('success', 'Activity added successfully');
    }

    /**
     * Display the specified case activity.
     *
     * @param  \App\Models\Case  $case
     * @param  \App\Models\CaseActivity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(CivilCase $case, CaseActivity $activity)
    {
        return view('cases.activities.show', compact('case', 'activity'));
    }

    /**
     * Show the form for editing the specified case activity.
     *
     * @param  \App\Models\Case  $case
     * @param  \App\Models\CaseActivity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(CivilCase $case, CaseActivity $activity)
    {
        return view('cases.activities.edit', compact('case', 'activity'));
    }

    /**
     * Update the specified case activity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Case  $case
     * @param  \App\Models\CaseActivity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CivilCase $case, CaseActivity $activity)
    {
        $validated = $request->validate([
            'activity_type' => 'required|string|max:50',
            'activity_date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document' => 'nullable|file|max:10240', // 10MB max file size
        ]);
        
        // Handle document upload if provided
        if ($request->hasFile('document')) {
            // Delete old document if exists
            if ($activity->document_path) {
                Storage::delete($activity->document_path);
            }
            
            $path = $request->file('document')->store('case_documents/' . $case->id);
            $activity->document_path = $path;
            $activity->document_name = $request->file('document')->getClientOriginalName();
        }
        
        $activity->update($validated);
        
        return redirect()->route('cases.activities.show', [$case, $activity])
                         ->with('success', 'Activity updated successfully');
    }

    /**
     * Remove the specified case activity from storage.
     *
     * @param  \App\Models\Case  $case
     * @param  \App\Models\CaseActivity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(CivilCase $case, CaseActivity $activity)
    {
        // Check if user has permission
        $this->authorize('delete', $activity);
        
        // Delete associated document if exists
        if ($activity->document_path) {
            Storage::delete($activity->document_path);
        }
        
        $activity->delete();
        
        return redirect()->route('cases.activities.index', $case)
                         ->with('success', 'Activity deleted successfully');
    }
    
    /**
     * Download the document associated with the case activity.
     *
     * @param  \App\Models\Case  $case
     * @param  \App\Models\CaseActivity  $activity
     * @return \Illuminate\Http\Response
     */
    public function downloadDocument(CivilCase $case, CaseActivity $activity)
    {
        if (!$activity->document_path) {
            abort(404, 'Document not found');
        }
        
        return Storage::download(
            $activity->document_path, 
            $activity->document_name
        );
    }
}