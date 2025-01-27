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
    public function create()
    {
        $cases = $this->criminalCaseRepository->pluck();
        $islands = $this->islandRepository->pluck();
        $lawyers = $this->userRepository->pluck();
        return view('oag.incident.create')
            ->with('cases', $cases)
            ->with('islands', $islands)
            ->with('lawyers', $lawyers);
    }

    /**
     * Store a newly created incident in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'lawyer_id' => 'required|exists:users,id',
            'island_id' => 'required|exists:islands,id',
            'date_of_incident_start' => 'nullable|date',
            'date_of_incident_end' => 'nullable|date',
            'place_of_incident' => 'required|string|max:255',
        ]);

        $data['created_by'] = auth()->id();
        $data['updated_by'] = null;

        $incident = $this->incidentRepository->create($data);

        return redirect()->route('crime.incident.index')->with('success', 'Incident created successfully.');
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
