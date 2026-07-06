<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\AccusedRepository;
use App\Repositories\Oag\Crime\IslandRepository;
use App\Repositories\Oag\Crime\UserRepository;
use App\Repositories\Oag\Crime\ReasonsForClosureRepository;
use App\Repositories\Oag\Crime\OffenceRepository;
use App\Repositories\Oag\Crime\OffenceCategoryRepository;
use App\Repositories\Oag\Crime\CourtRepository;
use App\Repositories\Oag\Crime\CaseReviewRepository;
use App\Repositories\Oag\Crime\CourtCaseRepository;
use App\Repositories\Oag\Crime\AppealDetailRepository;
use App\Repositories\Oag\Crime\CaseReallocationRepository;
use App\Repositories\Oag\Crime\CourtOfAppealRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\OAG\Crime\CaseReallocation;

use DataTables;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;


class CriminalCaseController extends Controller
{
    use AuthorizesCriminalCase;

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
    protected $courtOfAppealRepository;

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
    public function __construct(CourtOfAppealRepository $courtOfAppealRepository,CaseReallocationRepository $caseReallocationRepository,AppealDetailRepository $appealDetailRepository,CourtCaseRepository $courtCaseRepository, CaseReviewRepository $caseReviewRepository,
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
        $this->courtOfAppealRepository = $courtOfAppealRepository;
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
           'lawyer_id'              => ['nullable', 'exists:users,id', new \App\Rules\UserHasRole('cm.user')],
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

        $this->assertCaseIsActionable($criminalCase);

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

        $this->assertCaseIsActionable($criminalCase);

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

        if (!$criminalCase) {
            return redirect()->route('crime.criminalCase.index')
                ->with('error', 'Criminal Case not found');
        }

        $this->assertCaseIsActionable($criminalCase);

        // Validation rules
        $data = $request->validate([
            'case_file_number'      => 'required|string|max:255|unique:cases,case_file_number,' . $criminalCase->id,
            'date_file_received'    => 'required|date',
            'case_name'             => 'required|string|max:255',
            'date_of_incident'    => 'nullable|date',
            'date_file_closed'      => 'nullable|date',
            'reason_for_closure_id' => 'nullable|exists:reasons_for_closure,id',
            'lawyer_id'             => ['required', 'exists:users,id', new \App\Rules\UserHasRole('cm.user')],
            'island_id'             => 'required|exists:islands,id',
            'court_case_number'     => 'nullable|string|max:255',
        ]);
    
        $data['updated_by'] = auth()->id(); // Track who updated it
    
        // Check if lawyer_id has changed
        if ((int) $data['lawyer_id'] !== (int) $criminalCase->lawyer_id) {
            $data['status'] = 'reallocated';
            \Log::info('Status changed to reallocated');
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
        $criminalCase = $this->criminalCaseRepository->getById($id);

        if (!$criminalCase) {
            return redirect()->route('crime.criminalCase.index')
                ->with('error', 'Criminal Case not found');
        }

        $this->assertCaseIsActionable($criminalCase);

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
    $user = auth()->user();

    if (!$user->hasRole('cm.user')) {
        abort(403);
    }

    $this->assertCanActOnCase($case, $user);
    abort_unless(in_array($case->status, ['allocated', 'reallocated']), 403, 'This case must be allocated to a lawyer before it can be accepted or rejected.');

    $case->status = 'accepted';
    $case->rejection_reason = null;
    $case->accepted_at = now();
    $case->accepted_by = $user->id;
    $case->save();

    return back()->with('success', 'Case accepted successfully.');
}

public function reject(Request $request, $id)
{
    $request->validate(['rejection_reason' => 'required|string']);
    $case = $this->criminalCaseRepository->getById($id);
    $user = auth()->user();

    if (!$user->hasRole('cm.user')) {
        abort(403);
    }

    $this->assertCanActOnCase($case, $user);
    abort_unless(in_array($case->status, ['allocated', 'reallocated']), 403, 'This case must be allocated to a lawyer before it can be accepted or rejected.');

    $case->status = 'rejected';
    $case->rejection_reason = $request->rejection_reason;
    $case->rejected_at = now();
    $case->rejected_by = $user->id;
    $case->save();

    return back()->with('success', 'Case rejected with reason.');
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
        'to_lawyer_id' => ['required', 'exists:users,id', new \App\Rules\UserHasRole('cm.user')],
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
            abort_if(!$case, 404);
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
    $this->assertCanViewRelatedRecords();

    $case = $this->criminalCaseRepository->getById($id);
    abort_if(!$case, 404);
    $this->assertCaseIsActionable($case);

    $caseReviews = $this->caseReviewRepository->getReviewsByCaseId($id);

    // dd($caseReviews);

    return view('oag.crime.case_reviews.reviewed', compact('caseReviews'));
}

public function showCourtCases($id)
{
    $this->assertCanViewRelatedRecords();

    $case = $this->criminalCaseRepository->getById($id);
    abort_if(!$case, 404);
    $this->assertCaseIsActionable($case);

    $courtCases = $this->courtCaseRepository->getCourtCasesByCaseId($id);

    // dd($courtCases);

    return view('oag.crime.case_reviews.courtcase', compact('courtCases'));
}

public function showAppealCases($id)
{
    $this->assertCanViewRelatedRecords();

    $case = $this->criminalCaseRepository->getById($id);
    abort_if(!$case, 404);
    $this->assertCaseIsActionable($case);

    $appealDetails = $this->appealDetailRepository->getAppealDetailsByCaseId($id);

    // dd($appealDetails);

    return view('oag.crime.appeal_details.appeal', compact('appealDetails'));
}


public function showcourtofappealcase($id)
{
    $this->assertCanViewRelatedRecords();

    $case = $this->criminalCaseRepository->getById($id);
    abort_if(!$case, 404);
    $this->assertCaseIsActionable($case);

    $appealDetails = $this->courtOfAppealRepository->getCourtOfAppealByCaseId($id);

    // dd($appealDetails);

    return view('oag.crime.court_of_appeals.appeal', compact('appealDetails'));
}

/**
 * Unified tabbed view of all four related-record types for a case
 * (Reviewed Cases, Court Cases, Appeal Cases, Court of Appeal Cases),
 * replacing the old dropdown of separate pages with one page.
 *
 * @param int $id Criminal case ID
 * @return \Illuminate\View\View
 */
public function showRelatedRecords($id)
{
    $this->assertCanViewRelatedRecords();

    $case = $this->criminalCaseRepository->getById($id);
    abort_if(!$case, 404);
    $this->assertCaseIsActionable($case);

    $caseReviews = $this->caseReviewRepository->getReviewsByCaseId($id);
    $courtCases = $this->courtCaseRepository->getCourtCasesByCaseId($id);
    $appealDetails = $this->appealDetailRepository->getAppealDetailsByCaseId($id);
    $courtOfAppeals = $this->courtOfAppealRepository->getCourtOfAppealByCaseId($id);

    return view('oag.crime.related_records.index', compact(
        'case',
        'caseReviews',
        'courtCases',
        'appealDetails',
        'courtOfAppeals'
    ));
}

/**
 * Consolidated chronological timeline for a case: registration through
 * case review, court case, appeal, and court of appeal, all merged into
 * one ordered list instead of separate tabs.
 *
 * @param int $id Criminal case ID
 * @return \Illuminate\View\View
 */
public function showCaseTimeline($id)
{
    $this->assertCanViewRelatedRecords();

    $case = $this->criminalCaseRepository->getById($id);
    abort_if(!$case, 404);
    $this->assertCaseIsActionable($case);

    $case->load(['lawyer', 'island', 'accused.island', 'victims.island', 'offences', 'incidents.island', 'acceptedBy', 'rejectedBy', 'allocatedBy']);

    // Case progression order, used as a tiebreaker below: several of these
    // dates (e.g. charge_file_dated, appeal_filing_date) are DATE-only columns
    // that carry an implicit midnight time, while others (review_date) carry
    // a real time-of-day. Sorting purely by timestamp can then put a same-day
    // review after a same-day court filing purely because 00:00:00 < 22:09:00,
    // even though a review must always precede the filing it led to. The rank
    // below breaks ties by where the stage actually sits in the case's
    // lifecycle instead of by incidental time-of-day.
    $stageRank = [
        'Case Registered' => 1,
        'Case Allocated' => 2,
        'Case Accepted' => 3,
        'Case Rejected' => 3,
        'Case Reviewed' => 4,
        'Court Case Filed' => 5,
        'Court Case Judgment' => 6,
        'Appeal Filed' => 7,
        'Court of Appeal Filed' => 8,
        'Court of Appeal Judgment' => 9,
    ];

    $events = [];

    $events[] = [
        'date' => $case->date_file_received,
        'stage' => 'Case Registered',
        'icon' => 'fa-folder-open',
        'color' => 'primary',
        'summary' => "Case file {$case->case_file_number} received.",
    ];

    if ($case->date_of_allocation) {
        $events[] = [
            'date' => $case->date_of_allocation,
            'stage' => 'Case Allocated',
            'icon' => 'fa-user-check',
            'color' => 'primary',
            'summary' => trim(($case->lawyer ? 'Allocated to ' . $case->lawyer->name . '. ' : '')
                . ($case->allocatedBy ? 'Allocated by ' . $case->allocatedBy->name : '')),
        ];
    }

    if ($case->status === 'accepted' || $case->status === 'rejected') {
        // Prefer the precise accepted_at/rejected_at stamp; fall back to
        // updated_at only for cases accepted/rejected before those columns
        // existed, since that's the closest approximation available for them.
        $events[] = [
            'date' => $case->status === 'accepted'
                ? ($case->accepted_at ?? $case->updated_at)
                : ($case->rejected_at ?? $case->updated_at),
            'stage' => $case->status === 'accepted' ? 'Case Accepted' : 'Case Rejected',
            'icon' => $case->status === 'accepted' ? 'fa-check-circle' : 'fa-times-circle',
            'color' => $case->status === 'accepted' ? 'success' : 'danger',
            'summary' => $case->status === 'accepted'
                ? ($case->acceptedBy ? 'Accepted by ' . $case->acceptedBy->name : null)
                : trim(($case->rejection_reason ?? '')
                    . ($case->rejectedBy ? ' — rejected by ' . $case->rejectedBy->name : '')),
        ];
    }

    // A case is only ever closed via a review (insufficient evidence /
    // returned to police), so the closure reason lives on case_reviews, not
    // on cases itself — captured here for display in Particulars of the Case.
    $closureReasonDescription = null;
    $dateFileClosed = null;

    foreach ($this->caseReviewRepository->getReviewsByCaseId($id) as $review) {
        $events[] = [
            'date' => $review->review_date,
            'stage' => 'Case Reviewed',
            'icon' => 'fa-clipboard-check',
            'color' => 'success',
            'summary' => 'Evidence status: ' . ucfirst(str_replace('_', ' ', $review->evidence_status))
                . ($review->created_by_name ? ' — reviewed by ' . $review->created_by_name : ''),
        ];

        if (in_array($review->evidence_status, ['insufficient_evidence', 'returned_to_police'])) {
            $closureReasonDescription = $review->closure_reason_description;
            $dateFileClosed = $review->date_file_closed;
        }
    }

    foreach ($this->courtCaseRepository->getCourtCasesByCaseId($id) as $courtCase) {
        $events[] = [
            'date' => $courtCase->charge_file_dated,
            'stage' => 'Court Case Filed',
            'icon' => 'fa-gavel',
            'color' => 'warning',
            'summary' => trim(($courtCase->high_court_case_number ? 'High Court Case ' . $courtCase->high_court_case_number . '. ' : '')
                . ($courtCase->verdict ? 'Verdict: ' . ucfirst(str_replace('_', ' ', $courtCase->verdict)) . '. ' : '')
                . ($courtCase->court_outcome ? 'Outcome: ' . ucfirst($courtCase->court_outcome) . '.' : '')),
        ];

        if ($courtCase->judgment_delivered_date) {
            $events[] = [
                'date' => $courtCase->judgment_delivered_date,
                'stage' => 'Court Case Judgment',
                'icon' => 'fa-gavel',
                'color' => 'warning',
                'summary' => $courtCase->court_outcome ? 'Outcome: ' . ucfirst($courtCase->court_outcome) : null,
            ];
        }
    }

    $appealDetails = $this->appealDetailRepository->getAppealDetailsByCaseId($id);

    foreach ($appealDetails as $appeal) {
        $events[] = [
            'date' => $appeal->appeal_filing_date,
            'stage' => 'Appeal Filed',
            'icon' => 'fa-balance-scale',
            'color' => 'danger',
            'summary' => trim(($appeal->appeal_case_number ? 'Appeal Case ' . $appeal->appeal_case_number . '. ' : '')
                . ($appeal->verdict ? 'Verdict: ' . ucfirst(str_replace('_', ' ', $appeal->verdict)) . '. ' : '')
                . ($appeal->court_outcome ? 'Outcome: ' . ucfirst($appeal->court_outcome) . '.' : '')),
        ];
    }

    foreach ($this->courtOfAppealRepository->getCourtOfAppealByCaseId($id) as $coa) {
        $events[] = [
            'date' => $coa->appeal_filing_date,
            'stage' => 'Court of Appeal Filed',
            'icon' => 'fa-gavel',
            'color' => 'success',
            'summary' => trim(($coa->appeal_case_number ? 'Appeal Case ' . $coa->appeal_case_number . '. ' : '')
                . ($coa->court_outcome ? 'Outcome: ' . ucfirst($coa->court_outcome) . '.' : '')),
        ];

        if ($coa->judgment_delivered_date) {
            $events[] = [
                'date' => $coa->judgment_delivered_date,
                'stage' => 'Court of Appeal Judgment',
                'icon' => 'fa-gavel',
                'color' => 'success',
                'summary' => $coa->court_outcome ? 'Outcome: ' . ucfirst($coa->court_outcome) : null,
            ];
        }
    }

    // The Register of Proceedings represents a fixed legal-process pipeline
    // (registered -> allocated -> accepted/rejected -> reviewed -> court case
    // -> appeal -> court of appeal), so stage order is the primary sort key.
    // Recorded dates are not reliable as the primary key: they're entered
    // independently per module and can be out of real-world sequence (e.g. a
    // court case's charge_file_dated entered earlier than the case's own
    // date_file_received). Date is only used to order events that share the
    // same stage (e.g. two appeals).
    usort($events, function ($a, $b) use ($stageRank) {
        $rankA = $stageRank[$a['stage']] ?? 99;
        $rankB = $stageRank[$b['stage']] ?? 99;

        if ($rankA !== $rankB) {
            return $rankA <=> $rankB;
        }

        return strtotime($a['date'] ?? 'now') <=> strtotime($b['date'] ?? 'now');
    });

    return view('oag.crime.related_records.timeline', compact('case', 'events', 'closureReasonDescription', 'dateFileClosed', 'appealDetails'));
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
    abort_if(!$case, 404);
    $user = auth()->user();

    $this->assertCaseIsActionable($case);

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
        'to_lawyer_id' => ['required', 'exists:users,id', new \App\Rules\UserHasRole('cm.user')],
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
        $updateData = [
            'reallocation_reason' => $validated['reallocation_reason'],
            'reallocation_date' => $validated['reallocation_date'],
            'lawyer_id' => $request->to_lawyer_id,
            'status' => $isReallocation ? 'reallocated' : 'allocated',
            'updated_by' => $user->id,
        ];

        // Only stamp the original allocation event once — a reallocation
        // changes the assigned lawyer but is not a new "Case Allocated" event.
        if (!$isReallocation) {
            $updateData['date_of_allocation'] = $validated['reallocation_date'];
            $updateData['allocated_by'] = $user->id;
        }

        $this->criminalCaseRepository->update($case->id, $updateData);
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