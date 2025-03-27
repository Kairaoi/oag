<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\OAG\Crime\AccusedRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\IslandRepository;

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
        OffenceRepository $offenceRepository, OffenceCategoryRepository $offenceCategoryRepository)
    {
        $this->accusedRepository = $accusedRepository;
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->islandRepository = $islandRepository;
       
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
        
        $offencesByCategory = $this->offenceRepository->pluck(); // Ensure this contains data as expected
        $categories = $this->offenceCategoryRepository->pluck();
    
        return view('oag.accused.create')
            ->with('islands', $islands)
            
            ->with('cases', $cases)
            ->with('offencesByCategory', $offencesByCategory); // Correct variable name
    }
    

    /**
 * Store a newly created accused in storage.
 *
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function store(Request $request)
{
    \Log::info('Store method called'); // Basic log entry to confirm method execution
    
    // Validate request data
    $data = $request->validate([
        'case_id' => 'required|exists:cases,id',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'accused_particulars' => 'required|string',
        'gender' => 'required|in:Male,Female,Other',
        'date_of_birth' => 'required|date',
        'offences' => 'nullable|array',
        'offences.*' => 'exists:offences,id',
        'custom_offence' => 'nullable|string|max:255',
    ]);
    
    \Log::info('Validated Data:', $data);
    
    $data['created_by'] = auth()->id(); 
    $data['updated_by'] = null;
    
    $accused = $this->accusedRepository->create($data);
    
    \Log::info('Request Data:', $request->all());
    
    if (!empty($data['offences'])) {
        $accused->offences()->attach($data['offences']);
    }
    
    // Check if custom offence is provided
    if (!empty($data['custom_offence'])) {
        $accused->custom_offence = $data['custom_offence'];
        $accused->save();
    }
    
    // Check if we should continue to victim creation
    if ($request->has('continue_to_victim') && $request->input('continue_to_victim') == 1) {
        return redirect()->route('crime.criminalCase.createVictim', $data['case_id'])
            ->with('success', 'Accused created successfully. Please add victim details now.');
    }
    
    // If adding another accused, stay on the accused creation form with the same case ID
    if ($request->has('add_another_accused') && $request->input('add_another_accused') == 1) {
        return redirect()->route('crime.criminalCase.createAccused', $data['case_id'])
            ->with('success', 'Accused created successfully. Add another accused.');
    }
    
    // Default: go to accused index
    return redirect()->route('crime.accused.index')
        ->with('success', 'Accused created successfully.');
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
       
        $cases = $this->criminalCaseRepository->pluck();

        if (!$accused) {
            return response()->json(['message' => 'Accused not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.accused.show')
        ->with('cases', $cases)
        ->with('islands', $islands)
       
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
       
        $offencesByCategory = $this->offenceRepository->pluck();
        $categories = $this->offenceCategoryRepository->pluck();
    
        return view('oag.accused.edit')
            ->with('accused', $accused) // Pass the accused record
            ->with('islands', $islands)
            
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
   /**
 * Update the specified accused in storage.
 *
 * @param Request $request
 * @param int $id
 * @return Response
 */
public function update(Request $request, $id)
{
    \Log::info('Update method called for accused ID: ' . $id);
    
    // Find the accused record first
    $accused = $this->accusedRepository->getById($id);
    
    if (!$accused) {
        \Log::error('Accused not found with ID: ' . $id);
        return redirect()->route('crime.accused.index')
            ->with('error', 'Accused not found');
    }
    
    // Validate request data
    $data = $request->validate([
        'case_id' => 'required|exists:cases,id',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'accused_particulars' => 'required|string',
        'gender' => 'required|in:Male,Female,Other',
        'date_of_birth' => 'required|date',
        'offences' => 'nullable|array',
        'offences.*' => 'exists:offences,id',
        'custom_offence' => 'nullable|string|max:255',
    ]);
    
    \Log::info('Validated update data:', $data);
    
    // Set updated_by to current user
    $data['updated_by'] = auth()->id();
    
    // Update the accused record
    $updated = $this->accusedRepository->update($id, $data);
    
    if (!$updated) {
        \Log::error('Failed to update accused with ID: ' . $id);
        return redirect()->route('crime.accused.index')
            ->with('error', 'Failed to update accused');
    }
    
    // Handle offences relationship
    if (isset($data['offences'])) {
        try {
            // Use sync instead of attach for updates to prevent duplicates
            $accused->offences()->sync($data['offences']);
            
            // Log the synced offences for debugging
            $syncedOffences = $accused->offences()->select('offences.id as offence_id')->pluck('offence_id')->toArray();
            \Log::info('Synced offences for accused ID ' . $id . ':', $syncedOffences);
        } catch (\Exception $e) {
            \Log::error('Error syncing offences: ' . $e->getMessage());
            // Continue execution even if syncing fails
        }
    } else {
        // If no offences selected, detach all
        $accused->offences()->detach();
        \Log::info('All offences detached for accused ID: ' . $id);
    }
    
    // Handle custom offence if provided
    if (!empty($data['custom_offence'])) {
        try {
            // Save custom offence to the accused
            $accused->custom_offence = $data['custom_offence'];
            $accused->save();
            \Log::info('Custom offence saved for accused ID ' . $id . ': ' . $data['custom_offence']);
        } catch (\Exception $e) {
            \Log::error('Error saving custom offence: ' . $e->getMessage());
        }
    } else if (isset($data['custom_offence'])) {
        // Clear custom offence if empty string was submitted
        $accused->custom_offence = null;
        $accused->save();
        \Log::info('Custom offence cleared for accused ID: ' . $id);
    }
    
    \Log::info('Accused updated successfully. ID: ' . $id);
    
    return redirect()->route('crime.accused.index')
        ->with('success', 'Accused updated successfully.');
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
