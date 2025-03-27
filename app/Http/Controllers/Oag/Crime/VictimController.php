<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\OAG\Crime\VictimRepository;
use App\Repositories\OAG\Crime\CriminalCaseRepository;
use App\Repositories\OAG\Crime\IslandRepository;
use App\Repositories\Oag\Crime\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class VictimController extends Controller
{
    protected $victimRepository;
    protected $criminalCaseRepository;
    protected $islandRepository;
    protected $userRepository;

    /**
     * VictimController constructor.
     *
     * @param VictimRepository $victimRepository
     * @param CriminalCaseRepository $criminalCaseRepository
     * @param IslandRepository $islandRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        VictimRepository $victimRepository,
        CriminalCaseRepository $criminalCaseRepository,
        IslandRepository $islandRepository,
        UserRepository $userRepository
    ) {
        $this->victimRepository = $victimRepository;
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->islandRepository = $islandRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Get data for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->victimRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of the victims.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('oag.victim.index');
    }

    /**
     * Show the form for creating a new victim.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $cases = $this->criminalCaseRepository->pluck();
        $islands = $this->islandRepository->pluck();
        $councils = $this->userRepository->pluck();
        
        return view('oag.victim.create', [
            'islands' => $islands,
            'councils' => $councils,
            'cases' => $cases,
            'selected_case_id' => null
        ]);
    }
    
    /**
     * Show form for creating a victim for a specific case.
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
        $councils = $this->userRepository->pluck();
        
        // If lawyer is assigned to case, pre-select them
        $preSelectedLawyer = $criminalCase->lawyer_id ?? null;
        
        return view('oag.victim.create', [
            'islands' => $islands,
            'councils' => $councils,
            'cases' => $cases,
            'selected_case_id' => $id,
            'selected_lawyer_id' => $preSelectedLawyer
        ]);
    }

    /**
     * Store a newly created victim in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
 * Store a newly created victim in storage.
 *
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function store(Request $request)
{
    $data = $request->validate([
        'case_id' => 'required|exists:cases,id',
        'lawyer_id' => 'required|exists:users,id',
        'island_id' => 'required|exists:islands,id',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'victim_particulars' => 'required|string',
        'gender' => 'required|in:Male,Female,Other',
        'date_of_birth' => 'required|date',
        'age_group' => 'required|in:Under 13,Under 15,Under 18,Above 18',
    ]);
    // Add additional fields
    $data['created_by'] = auth()->id();
    $data['updated_by'] = null;

    // Create the victim
    $victim = $this->victimRepository->create($data);

    // For debugging
    \Log::info('Victim created', ['id' => $victim->id, 'case_id' => $data['case_id']]);
    \Log::info('Request has continue_to_incident?', ['has_flag' => $request->has('continue_to_incident')]);
    
    // Check if creation was successful
    if ($victim) {
        // ALWAYS redirect to incident creation view after creating a victim
        return redirect()->route('crime.criminalCase.createIncident', ['id' => $data['case_id']])
            ->with('success', 'Victim added successfully. Please add incident details now.');
    } else {
        return redirect()->back()
            ->with('error', 'Failed to create victim.');
    }
}
    /**
     * Display the specified victim.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        $victim = $this->victimRepository->getById($id);
        
        if (!$victim) {
            return redirect()->route('crime.victim.index')
                ->with('error', 'Victim not found');
        }
        
        $islands = $this->islandRepository->pluck();
        $councils = $this->userRepository->pluck();
        $cases = $this->criminalCaseRepository->pluck();

        return view('oag.victim.show', [
            'cases' => $cases,
            'islands' => $islands, 
            'councils' => $councils,
            'victim' => $victim
        ]);
    }

    /**
     * Show the form for editing the specified victim.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $victim = $this->victimRepository->getById($id);
        
        if (!$victim) {
            return redirect()->route('crime.victim.index')
                ->with('error', 'Victim not found');
        }

        $cases = $this->criminalCaseRepository->pluck();
        $islands = $this->islandRepository->pluck();
        $councils = $this->userRepository->pluck();
        
        // Format date for HTML date input
        if ($victim->date_of_birth) {
            $victim->date_of_birth = date('Y-m-d', strtotime($victim->date_of_birth));
        }
        
        return view('oag.victim.edit', [
            'victim' => $victim,
            'islands' => $islands,
            'councils' => $councils,
            'cases' => $cases
        ]);
    }

    /**
     * Update the specified victim in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Verify victim exists
        $victim = $this->victimRepository->getById($id);
        
        if (!$victim) {
            return redirect()->route('crime.victim.index')
                ->with('error', 'Victim not found');
        }
        
        $data = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'lawyer_id' => 'required|exists:users,id',
            'island_id' => 'required|exists:islands,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'victim_particulars' => 'required|string',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date',
            'age_group' => 'required|in:Under 13,Under 15,Under 18,Above 18',
        ]);

        $data['updated_by'] = auth()->id();

        $updated = $this->victimRepository->update($id, $data);

        if (!$updated) {
            return redirect()->back()
                ->with('error', 'Failed to update victim');
        }

        return redirect()->route('crime.victim.index')
            ->with('success', 'Victim updated successfully.');
    }

    /**
     * Remove the specified victim from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $deleted = $this->victimRepository->deleteById($id);

        if (!$deleted) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Victim not found or failed to delete'], Response::HTTP_NOT_FOUND);
            }
            
            return redirect()->route('crime.victim.index')
                ->with('error', 'Victim not found or failed to delete');
        }

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Victim deleted successfully']);
        }
        
        return redirect()->route('crime.victim.index')
            ->with('success', 'Victim deleted successfully.');
    }
}