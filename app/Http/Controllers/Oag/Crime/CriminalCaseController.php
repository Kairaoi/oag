<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\AccusedRepository; 
use App\Repositories\Oag\Crime\IslandRepository;
use App\Repositories\Oag\Crime\UserRepository;
use App\Repositories\Oag\Crime\ReasonsForClosureRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class CriminalCaseController extends Controller
{
    protected $criminalCaseRepository;
    protected $acussedRepository;
    protected $islandRepository;
    protected $userRepository;
    protected $reasonsForClosureRepository;

    /**
     * CriminalCaseController constructor.
     *
     * @param CriminalCaseRepository $criminalCaseRepository
     */
    public function __construct(CriminalCaseRepository $criminalCaseRepository, AccusedRepository $accusedRepository, IslandRepository $islandRepository, UserRepository $userRepository, ReasonsForClosureRepository $reasonsForClosureRepository )
{
    $this->criminalCaseRepository = $criminalCaseRepository;
    $this->accusedRepository = $accusedRepository;
    $this->islandRepository = $islandRepository;
    $this->userRepository = $userRepository;
    $this->reasonsForClosureRepository = $reasonsForClosureRepository;
}


    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->criminalCaseRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of the criminal cases.
     *
     * @return Response
     */
    public function index()
    {
    
        return view('oag.crime.index');
    }

    /**
     * Show the form for creating a new criminal case.
     *
     * @return Response
     */
    public function create()
    {
        $islands = $this->islandRepository->pluck();
        $lawyers  = $this->userRepository->pluck();
        $reasons = $this->reasonsForClosureRepository->pluck();
        return view('oag.crime.create')
    ->with('islands', $islands)
    ->with('reasons', $reasons)
    ->with('lawyers', $lawyers); // Corrected line

    }

    /**
     * Store a newly created criminal case in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
{
    $data = $request->validate([
        'case_file_number'      => 'required|string|max:255|unique:cases,case_file_number',
        'date_file_received'    => 'required|date',
        'case_name'             => 'required|string|max:255',
        'date_of_allocation'    => 'nullable|date',
        'date_file_closed'      => 'nullable|date',
        'reason_for_closure_id' => 'nullable|exists:reasons_for_closure,id',
        'lawyer_id'             => 'required|exists:users,id',
        'island_id'             => 'required|exists:islands,id',
      
    ]);

    $data['created_by'] = auth()->id(); // Set the current user as the creator
    $data['updated_by'] = null; // Initially set to null, updated by other methods later if needed

    $criminalCase = $this->criminalCaseRepository->create($data);

    return redirect()->route('crime.criminalCase.index')->with('success', 'Case created successfully.');
}


    /**
     * Display the specified criminal case.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $criminalCase = $this->criminalCaseRepository->getById($id);

        if (!$criminalCase) {
            return response()->json(['message' => 'Criminal Case not found'], Response::HTTP_NOT_FOUND);
        }

        // return response()->json($criminalCase);
        return view('oag.crime.show')->with('criminalCase',$criminalCase);
    }

    /**
     * Show the form for editing the specified criminal case.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $criminalCase = $this->criminalCaseRepository->getById($id);
    
        if (!$criminalCase) {
            return response()->json(['message' => 'Criminal Case not found'], Response::HTTP_NOT_FOUND);
        }
    
        // Ensure correct date formatting
        $criminalCase->date_file_received = $this->formatDate($criminalCase->date_file_received);
        $criminalCase->date_of_allocation = $this->formatDate($criminalCase->date_of_allocation);
        $criminalCase->date_file_closed = $this->formatDate($criminalCase->date_file_closed);
    
        $islands = $this->islandRepository->pluck();
        $lawyers = $this->userRepository->pluck();
        $reasons = $this->reasonsForClosureRepository->pluck();
    
        return view('oag.crime.edit')
            ->with('criminalCase', $criminalCase)
            ->with('islands', $islands)
            ->with('reasons', $reasons)
            ->with('lawyers', $lawyers);
    }
    

private function formatDate($date)
{
    // Check if date is a string and not null
    if ($date && is_string($date)) {
        try {
            // Create a DateTime object
            $dateTime = new \DateTime($date);
            return $dateTime->format('Y-m-d');
        } catch (\Exception $e) {
            // Handle the exception if the date is not valid
            return null;
        }
    }

    // Return null if date is not a valid string
    return null;
}

    

    /**
     * Update the specified criminal case in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
        'case_file_number'      => 'required|string|max:255|unique:cases,case_file_number',
        'date_file_received'    => 'required|date',
        'case_name'             => 'required|string|max:255',
        'date_of_allocation'    => 'nullable|date',
        'date_file_closed'      => 'nullable|date',
        'reason_for_closure_id' => 'nullable|exists:reasons_for_closure,id',
        'lawyer_id'             => 'required|exists:users,id',
        'island_id'             => 'required|exists:islands,id',
            
        ]);
    
        $updated = $this->criminalCaseRepository->update($id, $data);
    
        if (!$updated) {
            return response()->json(['message' => 'Criminal Case not found or failed to update'], Response::HTTP_NOT_FOUND);
        }
    
        return redirect()->route('crime.criminalCase..index')->with('success', 'Case created successfully.');
    }
    

    /**
     * Remove the specified criminal case from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->criminalCaseRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Criminal Case not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return redirect()->route('crime.criminalCase..index')->with('success', 'Case deleted successfully.');
    }
}
