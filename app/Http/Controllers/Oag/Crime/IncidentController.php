<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Crime\IncidentRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\IslandRepository;
use App\Repositories\Oag\Crime\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class IncidentController extends Controller
{
    protected $incidentRepository;
    protected $criminalCaseRepository;
    protected $islandRepository;
    protected $userRepository;

    /**
     * IncidentController constructor.
     *
     * @param IncidentRepository $incidentRepository
     * @param CriminalCaseRepository $criminalCaseRepository
     * @param IslandRepository $islandRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        IncidentRepository $incidentRepository, 
        CriminalCaseRepository $criminalCaseRepository,
        IslandRepository $islandRepository,
        UserRepository $userRepository
    ) {
        $this->incidentRepository = $incidentRepository;
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->islandRepository = $islandRepository;
        $this->userRepository = $userRepository;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->incidentRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of the incidents.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.incident.index');
    }

    /**
     * Show the form for creating a new incident.
     *
     * @return Response
     */
   /**
 * Show the form for creating a new incident.
 *
 * @return Response
 */
public function create()
{
    $cases = $this->criminalCaseRepository->pluck();
    $islands = $this->islandRepository->pluck();
    $lawyers = $this->userRepository->pluck();
    return view('oag.incident.create', [
        'cases' => $cases,
        'islands' => $islands,
        'lawyers' => $lawyers,
        'selected_case_id' => null,
        'selected_lawyer_id' => null
    ]);
}

    /**
 * Show form for creating an incident for a specific case.
 * 
 * @param int $id Criminal case ID
 * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
 */
public function createForCase($id)
{
    // Verify case exists
    $criminalCase = $this->criminalCaseRepository->getById($id);
    
    if (!$criminalCase) {
        return redirect()->route('crime.criminalCase.index')
            ->with('error', 'Criminal Case not found');
    }
    
    // Get necessary data for dropdowns
    $cases = $this->criminalCaseRepository->pluck();
    $islands = $this->islandRepository->pluck();
    $lawyers = $this->userRepository->pluck();
    
    // If lawyer is assigned to case, pre-select them
    $preSelectedLawyer = $criminalCase->lawyer_id ?? null;
    
    return view('oag.incident.create', [
        'cases' => $cases,
        'islands' => $islands,
        'lawyers' => $lawyers,
        'selected_case_id' => $id,
        'selected_lawyer_id' => $preSelectedLawyer
    ]);
}

   /**
 * Store a newly created incident in storage.
 *
 * @param Request $request
 * @return Response
 */
public function store(Request $request)
{
    // Validate request data
    $data = $request->validate([
        'case_id' => 'required|exists:cases,id',
        'lawyer_id' => 'required|exists:users,id',
        'island_id' => 'required|exists:islands,id',
        'date_of_incident_start' => 'required|date',
        'date_of_incident_end' => 'nullable|date|after_or_equal:date_of_incident_start',
        'place_of_incident' => 'required|string|max:255',
    ]);
    
    // Add tracking fields
    $data['created_by'] = auth()->id();
    $data['updated_by'] = null;
    
    // Optional logging
    \Log::info('Creating incident with data:', $data);
    
    // Create the incident
    $incident = $this->incidentRepository->create($data);

    if ($incident) {
        // Check if we should add another incident
        if ($request->has('add_another_incident') && $request->input('add_another_incident') == 1) {
            return redirect()->route('crime.criminalCase.createIncident', ['id' => $data['case_id']])
                ->with('success', 'Incident added successfully. Add another incident.');
        }
        
        // Check if we should return to case
        if ($request->has('return_to_case') && $request->input('return_to_case') == 1) {
            return redirect()->route('crime.criminalCase.show', $data['case_id'])
                ->with('success', 'Incident added successfully.');
        }
        
        // Default: go to incident index
        return redirect()->route('crime.incident.index')
            ->with('success', 'Incident created successfully.');
    } else {
        return redirect()->back()
            ->with('error', 'Failed to create incident.');
    }
}
    /**
     * Display the specified incident.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $incident = $this->incidentRepository->getById($id);
        $cases = $this->criminalCaseRepository->pluck();
        $islands = $this->islandRepository->pluck();
        $lawyers = $this->userRepository->pluck();

        if (!$incident) {
            return response()->json(['message' => 'Incident not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.incident.show')
            ->with('incident', $incident)
            ->with('cases', $cases)
            ->with('islands', $islands)
            ->with('lawyers', $lawyers);
    }

    /**
     * Show the form for editing the specified incident.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $incident = $this->incidentRepository->getById($id);
        $cases = $this->criminalCaseRepository->pluck();
        $islands = $this->islandRepository->pluck();
        $lawyers = $this->userRepository->pluck();

        return view('oag.incident.edit')
            ->with('incident', $incident)
            ->with('cases', $cases)
            ->with('islands', $islands)
            ->with('lawyers', $lawyers);
    }

    /**
     * Update the specified incident in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'lawyer_id' => 'required|exists:users,id',
            'island_id' => 'required|exists:islands,id',
            'date_of_incident_start' => 'nullable|date',
            'date_of_incident_end' => 'nullable|date',
            'place_of_incident' => 'required|string|max:255',
        ]);

        $data['updated_by'] = auth()->id();

        $updated = $this->incidentRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Incident not found or failed to update'], Response::HTTP_NOT_FOUND);
        }

        return redirect()->route('crime.incident.index')->with('success', 'Incident updated successfully.');
    }

    /**
     * Remove the specified incident from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->incidentRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Incident not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Incident deleted successfully']);
    }
}
