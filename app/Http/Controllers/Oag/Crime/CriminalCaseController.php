<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\AccusedRepository;
use App\Repositories\Oag\Crime\IslandRepository;
use App\Repositories\Oag\Crime\UserRepository;
use App\Repositories\Oag\Crime\ReasonsForClosureRepository;
use App\Repositories\Oag\Crime\OffenceRepository;
use App\Repositories\Oag\Crime\OffenceCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class CriminalCaseController extends Controller
{
    protected $criminalCaseRepository;
    protected $accusedRepository; // Fixed typo: acussedRepository → accusedRepository
    protected $islandRepository;
    protected $userRepository;
    protected $reasonsForClosureRepository;
    protected $offenceRepository;
    protected $offenceCategoryRepository;

    /**
     * CriminalCaseController constructor.
     *
     * @param CriminalCaseRepository $criminalCaseRepository
     * @param AccusedRepository $accusedRepository
     * @param IslandRepository $islandRepository
     * @param UserRepository $userRepository
     * @param ReasonsForClosureRepository $reasonsForClosureRepository
     * @param OffenceRepository $offenceRepository
     * @param OffenceCategoryRepository $offenceCategoryRepository
     */
    public function __construct(
        CriminalCaseRepository $criminalCaseRepository,
        AccusedRepository $accusedRepository,
        IslandRepository $islandRepository,
        UserRepository $userRepository,
        ReasonsForClosureRepository $reasonsForClosureRepository,
        OffenceRepository $offenceRepository,
        OffenceCategoryRepository $offenceCategoryRepository
    ) {
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->accusedRepository = $accusedRepository;
        $this->islandRepository = $islandRepository;
        $this->userRepository = $userRepository;
        $this->reasonsForClosureRepository = $reasonsForClosureRepository;
        $this->offenceRepository = $offenceRepository;
        $this->offenceCategoryRepository = $offenceCategoryRepository;
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
        $query = $this->criminalCaseRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of the criminal cases.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('oag.crime.index');
    }

    /**
     * Show the form for creating a new criminal case.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $islands = $this->islandRepository->pluck();
        $lawyers = $this->userRepository->pluck();
        $reasons = $this->reasonsForClosureRepository->pluck();
    
        return view('oag.crime.create')
            ->with('islands', $islands)
            ->with('reasons', $reasons)
            ->with('lawyers', $lawyers);
    }
    
    /**
     * Show the form for creating a new accused for a specific case.
     *
     * @param int $id Criminal case ID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function createAccused($id)
    {
        // Attempt to find the criminal case
        $criminalCase = $this->criminalCaseRepository->getById($id);
        
        // If case not found, redirect with an error
        if (!$criminalCase) {
            return redirect()->route('crime.criminalCase.index')
                ->with('error', 'Criminal Case not found');
        }
        
        // Fetch necessary dropdown data
        $cases = $this->criminalCaseRepository->pluck();
        $islands = $this->islandRepository->pluck();
        $offencesByCategory = $this->offenceCategoryRepository->groupOffencesByCategory();
        
        // Render the accused creation view with pre-selected case
        return view('oag.accused.create', [
            'cases' => $cases,
            'selected_case_id' => $id,
            'islands' => $islands,
            'offencesByCategory' => $offencesByCategory,
        ]);
    }
    
    /**
     * Store a newly created criminal case in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
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
            'court_case_number'     => 'nullable|string|max:255', // Added to match update method
        ]);
    
        $data['created_by'] = auth()->id(); // Set the current user as the creator
        $data['updated_by'] = null; // Initially set to null
    
        // Create the criminal case using the repository
        $criminalCase = $this->criminalCaseRepository->create($data);
    
        // Redirect to accused creation form with the newly created case ID
        return redirect()->route('crime.criminalCase.createAccused', $criminalCase->id)
            ->with('success', 'Case created successfully. Please add accused persons.');
    }
    
    /**
     * Display the specified criminal case.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        $criminalCase = $this->criminalCaseRepository->getById($id);

        if (!$criminalCase) {
            return redirect()->route('crime.criminalCase.index')
                ->with('error', 'Criminal Case not found');
        }

        return view('oag.crime.show')->with('criminalCase', $criminalCase);
    }

    /**
     * Show the form for editing the specified criminal case.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $criminalCase = $this->criminalCaseRepository->getById($id);
    
        if (!$criminalCase) {
            return redirect()->route('crime.criminalCase.index')
                ->with('error', 'Criminal Case not found');
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
    
    /**
     * Format a date to Y-m-d or return null if invalid.
     *
     * @param mixed $date
     * @return string|null
     */
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Get the current case to exclude its case_file_number from unique validation
        $criminalCase = $this->criminalCaseRepository->getById($id);
        
        if (!$criminalCase) {
            return redirect()->route('crime.criminalCase.index')
                ->with('error', 'Criminal Case not found');
        }

        // Validate with exclusion for current case number
        $data = $request->validate([
            'case_file_number'      => 'required|string|max:255|unique:cases,case_file_number,'.$id,
            'date_file_received'    => 'required|date',
            'case_name'             => 'required|string|max:255',
            'date_of_allocation'    => 'nullable|date',
            'date_file_closed'      => 'nullable|date',
            'reason_for_closure_id' => 'nullable|exists:reasons_for_closure,id',
            'lawyer_id'             => 'required|exists:users,id',
            'island_id'             => 'required|exists:islands,id',
            'court_case_number'     => 'nullable|string|max:255',
        ]);
        
        // Set updated_by to current user
        $data['updated_by'] = auth()->id();
        
        // Check for lawyer reallocation
        $isLawyerChanged = $criminalCase->lawyer_id != $data['lawyer_id'];
        
        // Update the criminal case
        $updated = $this->criminalCaseRepository->update($id, $data);
        
        if (!$updated) {
            return redirect()->route('crime.criminalCase.index')
                ->with('error', 'Failed to update case');
        }
        
        // If lawyer has changed, log the reallocation
        if ($isLawyerChanged) {
            $reallocation = [
                'case_id' => $id,
                'from_lawyer_id' => $criminalCase->lawyer_id,
                'to_lawyer_id' => $data['lawyer_id'],
                'reallocation_reason' => $request->input('reallocation_reason', 'Case reassigned during update'),
                'reallocation_date' => now(),
                'created_by' => auth()->id(),
            ];
            
            // Use a try/catch in case the case_reallocations functionality is needed
            try {
                \DB::table('case_reallocations')->insert($reallocation);
            } catch (\Exception $e) {
                // Log the error but continue with the update
                \Log::error('Failed to record case reallocation: ' . $e->getMessage());
            }
        }
        
        // Check if case is being closed
        $isBeingClosed = !$criminalCase->date_file_closed && $data['date_file_closed'];
        
        // Create case review entry if case is being closed
        if ($isBeingClosed && isset($data['reason_for_closure_id'])) {
            try {
                $reviewData = [
                    'case_id' => $id,
                    'evidence_status' => $request->input('evidence_status', 'insufficient_evidence'),
                    'review_notes' => $request->input('closure_notes', 'Case closed during update'),
                    'review_date' => now(),
                    'action_type' => 'review',
                    'created_by' => auth()->id(),
                ];
                
                \DB::table('case_reviews')->insert($reviewData);
            } catch (\Exception $e) {
                // Log the error but continue with the update
                \Log::error('Failed to create case review for closure: ' . $e->getMessage());
            }
        }

        return redirect()->route('crime.criminalCase.index')
            ->with('success', 'Case updated successfully.');
    }

    /**
     * Remove the specified criminal case from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $deleted = $this->criminalCaseRepository->deleteById($id);

        if (!$deleted) {
            return redirect()->route('crime.criminalCase.index')
                ->with('error', 'Criminal Case not found or failed to delete');
        }

        return redirect()->route('crime.criminalCase.index')
            ->with('success', 'Case deleted successfully.');
    }

    /**
 * Show the form for creating a new victim for a specific case.
 *
 * @param int $id
 * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
 */
public function createVictim($id)
{
    // Attempt to find the criminal case
    $criminalCase = $this->criminalCaseRepository->getById($id);
    
    // If case not found, redirect with an error
    if (!$criminalCase) {
        return redirect()->route('crime.criminalCase.index')
            ->with('error', 'Criminal Case not found');
    }
    
    // Call the victim controller's createForCase method
    return app(VictimController::class)->createForCase($id);
}
/**
 * Show the form for creating a new incident for a specific case.
 *
 * @param int $id
 * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
 */
public function createIncident($id)
{
    // Attempt to find the criminal case
    $criminalCase = $this->criminalCaseRepository->getById($id);
    
    // If case not found, redirect with an error
    if (!$criminalCase) {
        return redirect()->route('crime.criminalCase.index')
            ->with('error', 'Criminal Case not found');
    }
    
    // Call the incident controller's createForCase method
    return app(IncidentController::class)->createForCase($id);
}

}