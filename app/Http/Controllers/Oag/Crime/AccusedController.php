<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\OAG\Crime\AccusedRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\IslandRepository;
use App\Repositories\Oag\Crime\UserRepository;
use App\Repositories\Oag\Crime\OffenceRepository;
use App\Repositories\Oag\Crime\OffenceCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class AccusedController extends Controller
{
    protected $accusedRepository;
    protected $criminalCaseRepository;
    protected $islandRepository;
    protected $userRepository;
    protected $offenceRepository;
    protected $offenceCategoryRepository;

    /**
     * AccusedController constructor.
     *
     * @param AccusedRepository $accusedRepository
     */
    public function __construct(
        AccusedRepository $accusedRepository, 
        CriminalCaseRepository $criminalCaseRepository,
        IslandRepository $islandRepository,
        UserRepository $userRepository, OffenceRepository $offenceRepository, OffenceCategoryRepository $offenceCategoryRepository)
    {
        $this->accusedRepository = $accusedRepository;
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->islandRepository = $islandRepository;
        $this->userRepository = $userRepository;
        $this->offenceRepository = $offenceRepository;
        $this->offenceCategoryRepository = $offenceCategoryRepository;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->accusedRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of the accused.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.accused.index');
    }

    /**
     * Show the form for creating a new accused.
     *
     * @return Response
     */
    public function create()
    {
        $cases = $this->criminalCaseRepository->pluck();
        $islands = $this->islandRepository->pluck();
        $councils = $this->userRepository->pluck();
        $offencesByCategory = $this->offenceRepository->pluck(); // Ensure this contains data as expected
        $categories = $this->offenceCategoryRepository->pluck();
    
        return view('oag.accused.create')
            ->with('islands', $islands)
            ->with('councils', $councils)
            ->with('cases', $cases)
            ->with('offencesByCategory', $offencesByCategory); // Correct variable name
    }
    

    /**
     * Store a newly created accused in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        \Log::info('Store method called'); // Basic log entry to confirm method execution
    
        // Validate request data
        $data = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'lawyer_id' => 'required|exists:users,id',
            'island_id' => 'required|exists:islands,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'accused_particulars' => 'required|string',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date',
            'offences' => 'nullable|array',
            'offences.*' => 'exists:offences,id',
        ]);
    
        \Log::info('Validated Data:', $data);
    
        $data['created_by'] = auth()->id(); 
        $data['updated_by'] = null;
    
        $accused = $this->accusedRepository->create($data);
    
        \Log::info('Request Data:', $request->all());


    
        if (!empty($data['offences'])) {
            $accused->offences()->attach($data['offences']);
        }
    
        return redirect()->route('crime.accused.index')->with('success', 'Accused created successfully.');
    }
    
    



    /**
     * Display the specified accused.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $accused = $this->accusedRepository->getById($id);
        $islands = $this->islandRepository->pluck();
        $councils = $this->userRepository->pluck();
        $cases = $this->criminalCaseRepository->pluck();

        if (!$accused) {
            return response()->json(['message' => 'Accused not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.accused.show')
        ->with('cases', $cases)
        ->with('islands', $islands)
        ->with('councils', $councils)
        ->with('accused', $accused);
    }

    /**
     * Show the form for editing the specified accused.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        // Retrieve the specific accused record to edit
        $accused = $this->accusedRepository->getById($id);
    
        // Ensure the accused exists
        if (!$accused) {
            return redirect()->route('crime.accused.index')->with('error', 'Accused not found.');
        }
    
        // Retrieve necessary lists for the form
        $cases = $this->criminalCaseRepository->pluck();
        $islands = $this->islandRepository->pluck();
        $councils = $this->userRepository->pluck();
        $offencesByCategory = $this->offenceRepository->pluck();
        $categories = $this->offenceCategoryRepository->pluck();
    
        return view('oag.accused.edit')
            ->with('accused', $accused) // Pass the accused record
            ->with('islands', $islands)
            ->with('councils', $councils)
            ->with('cases', $cases)
            ->with('offencesByCategory', $offencesByCategory) // Correct variable name
            ->with('categories', $categories); // Include categories if necessary
    }
    

    /**
     * Update the specified accused in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        \Log::info('Update method called for Accused ID: ' . $id);
    
        // Validate request data
        $data = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'lawyer_id' => 'required|exists:users,id',
            'island_id' => 'required|exists:islands,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'accused_particulars' => 'required|string',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date',
            'offences' => 'required|array',
            'offences.*' => 'exists:offences,id',
        ]);
    
        // Log validated data
        \Log::info('Validated Data for Update:', $data);
    
        $data['updated_by'] = auth()->id(); 
    
        // Attempt to update the accused record
        $updated = $this->accusedRepository->update($id, $data);
    
        if (!$updated) {
            \Log::error('Failed to update Accused ID: ' . $id);
            return response()->json(['message' => 'Accused not found or failed to update'], Response::HTTP_NOT_FOUND);
        }
    
        // Log success message and updated data
        \Log::info('Accused updated successfully. Updated ID: ' . $id);
    
        $accused = $this->accusedRepository->getById($id);
    
        // Log accused details before syncing offences
        \Log::info('Accused Details Before Sync:', $accused->toArray());
    
        // Sync offences
        $accused->offences()->sync($data['offences']); 
    
        // Log offences after syncing
        \Log::info('Accused Offences After Sync:', $accused->offences()->pluck('id')->toArray());
    
        return redirect()->route('crime.accused.index')->with('success', 'Accused updated successfully.');
    }
    


    
    /**
     * Remove the specified accused from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->accusedRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Accused not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Accused deleted successfully']);
    }
}
