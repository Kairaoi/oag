<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\OAG\Crime\VictimRepository; // Changed repository
use App\Repositories\OAG\Crime\CriminalCaseRepository;
use App\Repositories\OAG\Crime\IslandRepository;
use App\Repositories\Oag\Crime\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class VictimController extends Controller
{
    protected $victimRepository; // Changed variable name
    protected $criminalCaseRepository;
    protected $islandRepository;
    protected $userRepository;

    /**
     * VictimController constructor.
     *
     * @param VictimRepository $victimRepository
     */
    public function __construct(
        VictimRepository $victimRepository, // Changed repository
        CriminalCaseRepository $criminalCaseRepository,
        IslandRepository $islandRepository,
        UserRepository $userRepository
    ) {
        $this->victimRepository = $victimRepository; // Changed variable name
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->islandRepository = $islandRepository;
        $this->userRepository = $userRepository;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->victimRepository->getForDataTable($search); // Changed repository method

        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of the victims.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.victim.index');
    }

    /**
     * Show the form for creating a new victim.
     *
     * @return Response
     */
    public function create()
    {
        $cases = $this->criminalCaseRepository->pluck();
        $islands = $this->islandRepository->pluck();
        $councils = $this->userRepository->pluck();
        return view('oag.victim.create')
            ->with('islands', $islands)
            ->with('councils', $councils)
            ->with('cases', $cases);
    }

    /**
     * Store a newly created victim in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
{
    // Validate data
    $data = $request->validate([
        'case_id' => 'required|exists:cases,id',
        'lawyer_id' => 'required|exists:users,id',
        'island_id' => 'required|exists:islands,id',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'victim_particulars' => 'required|string',
        'gender' => 'required|in:Male,Female,Other',
        'date_of_birth' => 'required|date',
    ]);

    // Add additional fields
    $data['created_by'] = auth()->id();
    $data['updated_by'] = null;

    // Debugging: log the data
    \Log::info('Victim data:', $data);

    // Create the victim
    $victim = $this->victimRepository->create($data);

    // Check if creation was successful
    if ($victim) {
        return redirect()->route('crime.victim.index')->with('success', 'Victim created successfully.');
    } else {
        return redirect()->back()->with('error', 'Failed to create victim.');
    }
}


    /**
     * Display the specified victim.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $victim = $this->victimRepository->getById($id); // Changed repository method
        $islands = $this->islandRepository->pluck();
        $councils = $this->userRepository->pluck();
        $cases = $this->criminalCaseRepository->pluck();

        if (!$victim) {
            return response()->json(['message' => 'Victim not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.victim.show')
            ->with('cases', $cases)
            ->with('islands', $islands)
            ->with('councils', $councils)
            ->with('victim', $victim); // Changed variable name
    }

    /**
     * Show the form for editing the specified victim.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $victim = $this->victimRepository->getById($id); // Changed repository method

        $cases = $this->criminalCaseRepository->pluck();
        $islands = $this->islandRepository->pluck();
        $councils = $this->userRepository->pluck();
        return view('oag.victim.edit')
            ->with('victim', $victim) // Changed variable name
            ->with('islands', $islands)
            ->with('councils', $councils)
            ->with('cases', $cases);
    }

    /**
     * Update the specified victim in storage.
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'victim_particulars' => 'required|string', // Changed field name
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date',
        ]);

        $data['updated_by'] = auth()->id();  // Set the current user as the updater

        $updated = $this->victimRepository->update($id, $data); // Changed repository method

        if (!$updated) {
            return response()->json(['message' => 'Victim not found or failed to update'], Response::HTTP_NOT_FOUND);
        }

        // Redirect to the index route with a success message
        return redirect()->route('crime.victim.index')->with('success', 'Victim Updated successfully.');
    }

    /**
     * Remove the specified victim from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->victimRepository->deleteById($id); // Changed repository method

        if (!$deleted) {
            return response()->json(['message' => 'Victim not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Victim deleted successfully']);
    }
}
