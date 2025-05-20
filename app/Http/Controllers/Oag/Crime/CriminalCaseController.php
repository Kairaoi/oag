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
use App\Repositories\Oag\Crime\AppealDetailRepository;
use App\Repositories\Oag\Crime\CaseReallocationRepository;
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
    protected $appealDetailRepository;
    protected $caseReallocationRepository;

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
    public function __construct(CaseReallocationRepository $caseReallocationRepository,AppealDetailRepository $appealDetailRepository,CourtCaseRepository $courtCaseRepository, CaseReviewRepository $caseReviewRepository,
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
        $this->appealDetailRepository = $appealDetailRepository;
        $this->caseReallocationRepository = $caseReallocationRepository;
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
            'date_of_incident'    => 'nullable|date',
            'date_file_closed'      => 'nullable|date',
            'reason_for_closure_id' => 'nullable|exists:reasons_for_closure,id',
           'lawyer_id'              => 'nullable|exists:users,id',
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
        $criminalCase->date_of_incident = $this->formatDate($criminalCase->date_of_incident);
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
            'date_of_incident'    => 'nullable|date',
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

/**
 * Store a newly created appeal case
 * 
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 */


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
                'status' => 'reallocated',
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

public function showAppealCases($id)
{
    $appealDetails = $this->appealDetailRepository->getAppealDetailsByCaseId($id);

    // dd($appealDetails);

    return view('oag.crime.appeal_details.appeal', compact('appealDetails'));
}

/**
 * Show the lawyer allocation form for a specific case.
 *
 * @param int $id Criminal case ID
 * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
 */
public function showAllocationForm($id)
{
    // Attempt to find the criminal case
    $criminalCase = $this->criminalCaseRepository->getById($id);
    
    // If case not found, redirect with an error
    if (!$criminalCase) {
        return redirect()->route('crime.criminalCase.index')
            ->with('error', 'Criminal Case not found');
    }
    
    // Get all lawyers
    $lawyers = $this->userRepository->pluck();
    
    // Return view with case and lawyers data
    return view('oag.crime.allocate_lawyer', compact('criminalCase', 'lawyers'));
}

/**
 * Process lawyer allocation to a specific case.
 *
 * @param Request $request
 * @param int $id Criminal case ID
 * @return \Illuminate\Http\RedirectResponse
 */
public function allocateLawyer(Request $request, $id)
{
    $case = $this->criminalCaseRepository->getById($id);
    $user = auth()->user();

    Log::info('Reallocation request received', [
        'case_id' => $id,
        'request' => $request->all()
    ]);
    Log::info('Authenticated user', [
        'user_id' => $user->id,
        'roles' => $user->roles->pluck('name')
    ]);
    Log::info('Criminal case fetched', [
        'lawyer_id' => $case->lawyer_id
    ]);

    $validated = $request->validate([
        'to_lawyer_id' => 'required|exists:users,id',
        'reallocation_reason' => 'nullable|string|max:1000',
        'reallocation_date' => 'required|date',
    ]);
    

    DB::beginTransaction();

    try {
        $isReallocation = !is_null($case->lawyer_id);

        if ($isReallocation) {
            // Reallocation case — record it
            $this->caseReallocationRepository->create([
                'case_id' => $case->id,
                'from_lawyer_id' => $case->lawyer_id,
                'to_lawyer_id' => $validated['to_lawyer_id'],
                'reallocation_reason' => $validated['reallocation_reason'],
                'reallocation_date' => $validated['reallocation_date'],
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        }

        // Update the case regardless
        $this->criminalCaseRepository->update($case->id, [
            'reallocation_reason' => $validated['reallocation_reason'], 
            'reallocation_date' => $validated['reallocation_date'], 
            'lawyer_id' => $request->to_lawyer_id,   
            'status' => 'allocated',
            'updated_by' => $user->id,
        ]);
        Log::info('Updating lawyer_id', [
            'lawyer_id' => $validated['to_lawyer_id']
        ]);
        
        DB::commit();

        return redirect()
            ->route('crime.criminalCase.index')
            ->with('success', $isReallocation ? 'Lawyer reallocated successfully.' : 'Lawyer allocated successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error during case reallocation', [
            'error' => $e->getMessage()
        ]);
        return back()->withErrors('An error occurred while allocating the lawyer.');
    }
}


}