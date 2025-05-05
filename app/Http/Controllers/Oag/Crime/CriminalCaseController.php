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
use App\Repositories\OAG\Crime\CourtRepository;
use App\Repositories\Oag\Crime\CaseReviewRepository;
use App\Repositories\Oag\Crime\CourtCaseRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Oag\Crime\CaseReallocation;

use DataTables;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;


class CriminalCaseController extends Controller
{
    protected $criminalCaseRepository;
    protected $accusedRepository; // Fixed typo: acussedRepository → accusedRepository
    protected $islandRepository;
    protected $userRepository;
    protected $reasonsForClosureRepository;
    protected $offenceRepository;
    protected $offenceCategoryRepository;
    protected $courtRepository;
    protected $caseReviewRepository;
    protected $courtCaseRepository;

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
    public function __construct(CourtCaseRepository $courtCaseRepository, CaseReviewRepository $caseReviewRepository,
        CriminalCaseRepository $criminalCaseRepository,
        AccusedRepository $accusedRepository,
        IslandRepository $islandRepository,
        UserRepository $userRepository,
        ReasonsForClosureRepository $reasonsForClosureRepository,
        OffenceRepository $offenceRepository,
        OffenceCategoryRepository $offenceCategoryRepository,
        CourtRepository $courtRepository
    ) {
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->accusedRepository = $accusedRepository;
        $this->islandRepository = $islandRepository;
        $this->userRepository = $userRepository;
        $this->reasonsForClosureRepository = $reasonsForClosureRepository;
        $this->offenceRepository = $offenceRepository;
        $this->offenceCategoryRepository = $offenceCategoryRepository;
        $this->courtRepository = $courtRepository;
        $this->caseReviewRepository = $caseReviewRepository;
        $this->courtCaseRepository = $courtCaseRepository;
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
        // Retrieve the criminal case
        $criminalCase = $this->criminalCaseRepository->getById($id);
    
        // Validation rules
        $data = $request->validate([
            'case_file_number'      => 'required|string|max:255|unique:cases,case_file_number,' . $criminalCase->id,
            'date_file_received'    => 'required|date',
            'case_name'             => 'required|string|max:255',
            'date_of_allocation'    => 'nullable|date',
            'date_file_closed'      => 'nullable|date',
            'reason_for_closure_id' => 'nullable|exists:reasons_for_closure,id',
            'lawyer_id'             => 'required|exists:users,id',
            'island_id'             => 'required|exists:islands,id',
            'court_case_number'     => 'nullable|string|max:255',
        ]);
    
        $data['updated_by'] = auth()->id(); // Track who updated it
    
        // Check if lawyer_id has changed
        if ((int) $data['lawyer_id'] !== (int) $criminalCase->lawyer_id) {
            $data['status'] = 'reallocate';
            \Log::info('Status changed to reallocate');
        }
    
        // Update the criminal case with the validated data
        // Ensure the status is updated correctly here
        $this->criminalCaseRepository->update($id, $data);
    
        return redirect()->route('crime.criminalCase.edit', $id)
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

/**
 * Show form for creating an appeal case
 * 
 * @return \Illuminate\View\View
 */
/**
 * Show form for creating an appeal case
 *
 * @param int|null $id The ID of the original case (optional)
 * @return \Illuminate\View\View
 */
public function createAppeal($id = null)
{
    if ($id) {
        $originalCase = $this->criminalCaseRepository->getById($id);
        
        if (!$originalCase || $originalCase->is_appeal_case || $originalCase->is_on_appeal) {
            return redirect()->route('crime.criminalCase.index')
                ->with('error', 'Te case ae rineaki e aki tau ibukin appeal.');
        }
        
        $originalCases = [$originalCase->id => $originalCase->case_name];
        
        // Prepare suggested values based on original case
        $suggestedValues = [
            'case_name' => "Appeal - {$originalCase->case_name}",
            'island_id' => $originalCase->island_id,
            'lawyer_id' => $originalCase->lawyer_id,
        ];
    } else {
        $originalCases = $this->criminalCaseRepository->getNonAppealCases();
        $suggestedValues = [];
    }
    
    $courtsOfAppeal = $this->courtRepository->pluck();
    $islands = $this->islandRepository->pluck();
    $lawyers = $this->userRepository->pluck();
    
    return view('oag.crime.create_appeal')
        ->with('originalCases', $originalCases)
        ->with('courtsOfAppeal', $courtsOfAppeal)
        ->with('islands', $islands)
        ->with('lawyers', $lawyers)
        ->with('selectedCaseId', $id)
        ->with('suggestedValues', $suggestedValues);
}
/**
 * Store a newly created appeal case
 * 
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function storeAppeal(Request $request)
{
    $validatedData = $request->validate([
        'original_case_id' => 'required|exists:cases,id',
        'case_file_number' => 'required|string|unique:cases,case_file_number',
        'case_name' => 'required|string',
        'date_file_received' => 'required|date',
        'lawyer_id' => 'required|exists:users,id',
        'island_id' => 'required|exists:islands,id',
        'court_id' => 'required|exists:courts,id',
        'appeal_grounds' => 'required|string',  // New field
    ]);
    
    \DB::beginTransaction();
    
    try {
        // Mark original case as on appeal
        $originalCase = $this->criminalCaseRepository->getById($request->original_case_id);
        $originalCase->update(['is_on_appeal' => true]);
        
        // Create the appeal case
        $appealData = $validatedData;
        $appealData['is_appeal_case'] = true;
        $appealData['created_by'] = auth()->id();
        
        $appealCase = $this->criminalCaseRepository->create($appealData);
        
        // Create appeal details record
        $appealDetails = [
            'case_id' => $appealCase->id,
            'appeal_case_number' => $validatedData['case_file_number'],
            'appeal_filing_date' => $validatedData['date_file_received'],
            'appeal_grounds' => $validatedData['appeal_grounds'] ?? 'Appeal grounds not specified',
            'appeal_status' => 'pending',
            'created_by' => auth()->id(),
        ];
        
        \DB::table('appeal_details')->insert($appealDetails);
        
        \DB::commit();
        
        return redirect()->route('crime.criminalCase.show', $appealCase->id)
            ->with('success', 'Appeal case created successfully');
    } catch (\Exception $e) {
        \DB::rollback();
        return redirect()->back()
            ->with('error', 'Error creating appeal case: ' . $e->getMessage())
            ->withInput();
    }
}

public function accept($id)
{
    $case = $this->criminalCaseRepository->getById($id);

    if (auth()->user()->hasRole('cm.user')) {
        $case->status = 'accepted';
        $case->rejection_reason = null;
        $case->save();

        return back()->with('success', 'Case accepted successfully.');
    }

    abort(403);
}

public function reject(Request $request, $id)
{
    $request->validate(['rejection_reason' => 'required|string']);
    $case = $this->criminalCaseRepository->getById($id);

    if (auth()->user()->hasRole('cm.user')) {
        $case->status = 'rejected';
        $case->rejection_reason = $request->rejection_reason;
        $case->save();

        return back()->with('success', 'Case rejected with reason.');
    }

    abort(403);
}

public function showReallocationForm($id)
{
    $case = $this->criminalCaseRepository->getById($id);
    $lawyers = $this->userRepository->pluck();
    return view('oag.crime.reallocate', compact('case', 'lawyers'));
}




public function reallocateCase(Request $request, $caseId)
{
    Log::info("Reallocation request received", ['case_id' => $caseId, 'request' => $request->all()]);

    $request->validate([
        'to_lawyer_id' => 'required|exists:users,id',
        'reallocation_reason' => 'required|string',
        'reallocation_date' => 'required|date',
    ]);

    $user = Auth::user();
    Log::info("Authenticated user", ['user_id' => $user->id, 'roles' => $user->getRoleNames()]);

    // Check if the user has the 'cm.user' role
    if (!$user->hasRole('cm.admin')) {
        Log::warning("User does not have permission to reallocate", ['user_id' => $user->id]);
        abort(403, 'Unauthorized action.');
    }

    try {
        DB::transaction(function () use ($request, $caseId, $user) {

            $case = $this->criminalCaseRepository->getById($caseId);
            Log::info("Criminal case fetched", ['lawyer_id' => $case->lawyer_id]);

            // Create a new reallocation record
            $reallocation = CaseReallocation::create([
                'case_id' => $case->id,
                'from_lawyer_id' => $case->lawyer_id,
                'to_lawyer_id' => $request->to_lawyer_id,
                'reallocation_reason' => $request->reallocation_reason,
                'reallocation_date' => $request->reallocation_date,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            Log::info("Case reallocation created", ['reallocation_id' => $reallocation->id]);

            // Update the lawyer in the criminal case itself
           // Update the lawyer in the criminal case itself and change status
            $case->update([
                'lawyer_id' => $request->to_lawyer_id,
                'updated_by' => $user->id,
                'status' => 'allocated',
            ]);


            Log::info("Case lawyer updated", ['new_lawyer_id' => $request->to_lawyer_id]);
        });

        return redirect()->route('crime.criminalCase.index')->with('success', 'Case reallocated successfully.');

    } catch (\Exception $e) {
        Log::error("Error during case reallocation", ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Reallocation failed. Please try again.');
    }
}

public function showReviewedCases($id)
{
    $caseReviews = $this->caseReviewRepository->getReviewsByCaseId($id);

    // dd($caseReviews);

    return view('oag.crime.case_reviews.reviewed', compact('caseReviews'));
}

public function showCourtCases($id)
{
    $courtCases = $this->courtCaseRepository->getCourtCasesByCaseId($id);

    // dd($courtCases);

    return view('oag.crime.case_reviews.courtcase', compact('courtCases'));
}



}