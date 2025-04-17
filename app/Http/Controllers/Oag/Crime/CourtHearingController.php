<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Crime\CourtHearingRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class CourtHearingController extends Controller
{
    protected $courtHearingRepository;
    protected $criminalCaseRepository;

    public function __construct(CourtHearingRepository $courtHearingRepository, CriminalCaseRepository $criminalCaseRepository)
    {
        $this->courtHearingRepository = $courtHearingRepository;
        $this->criminalCaseRepository = $criminalCaseRepository;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->courtHearingRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    public function index()
    {
        return view('oag.court_hearing.index');
    }

    public function create()
    {
        $cases = $this->criminalCaseRepository->pluck();
        return view('oag.court_hearing.create', compact('cases'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'hearing_date' => 'required|date',
            'hearing_type' => 'required|string|max:255',
            'hearing_notes' => 'nullable|string',
            'is_completed' => 'boolean',
            'has_verdict' => 'boolean',
            'verdict' => 'nullable|in:guilty,not_guilty,dismissed,withdrawn,other',
            'verdict_details' => 'nullable|string',
            'verdict_date' => 'nullable|date',
            'sentencing_details' => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();
        $data['updated_by'] = null;
        
        $this->courtHearingRepository->create($data);
        
        return redirect()->route('crime.court-hearings.index')->with('success', 'Court hearing created successfully.');
    }

    public function show($id)
    {
        $hearing = $this->courtHearingRepository->getById($id);
        if (!$hearing) {
            return response()->json(['message' => 'Court hearing not found'], Response::HTTP_NOT_FOUND);
        }
        return view('oag.court_hearing.show', compact('hearing'));
    }

    public function edit($id)
    {
        $hearing = $this->courtHearingRepository->getById($id);
        if (!$hearing) {
            return redirect()->route('crime.court-hearings.index')->with('error', 'Court hearing not found.');
        }
        $cases = $this->criminalCaseRepository->pluck();
        return view('oag.court_hearing.edit', compact('hearing', 'cases'));
    }

    public function update(Request $request, $id)
    {
        $hearing = $this->courtHearingRepository->getById($id);
        if (!$hearing) {
            return redirect()->route('crime.court-hearings.index')->with('error', 'Court hearing not found.');
        }

        $data = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'hearing_date' => 'required|date',
            'hearing_type' => 'required|string|max:255',
            'hearing_notes' => 'nullable|string',
            'is_completed' => 'boolean',
            'has_verdict' => 'boolean',
            'verdict' => 'nullable|in:guilty,not_guilty,dismissed,withdrawn,other',
            'verdict_details' => 'nullable|string',
            'verdict_date' => 'nullable|date',
            'sentencing_details' => 'nullable|string',
        ]);

        $data['updated_by'] = auth()->id();
        
        $this->courtHearingRepository->update($id, $data);
        
        return redirect()->route('crime.court-hearings.index')->with('success', 'Court hearing updated successfully.');
    }

    public function destroy($id)
    {
        $deleted = $this->courtHearingRepository->deleteById($id);
        if (!$deleted) {
            return response()->json(['message' => 'Court hearing not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['message' => 'Court hearing deleted successfully']);
    }
}
