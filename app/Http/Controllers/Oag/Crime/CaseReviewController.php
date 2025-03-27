<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Crime\CaseReviewRepository; // New repository for case reviews
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\UserRepository;
use App\Repositories\Oag\Crime\ReasonsForClosureRepository;
use App\Repositories\Oag\Crime\CaseReallocationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

use Illuminate\Support\Facades\Log;

class CaseReviewController extends Controller
{
    protected $caseReviewRepository; // New repository for case reviews
    protected $criminalCaseRepository;
    protected $userRepository;
    protected $reasonsForClosureRepository;
    protected $caseReallocationRepository;

    /**
     * CaseReviewController constructor.
     *
     * @param CaseReviewRepository $caseReviewRepository
     * @param CriminalCaseRepository $criminalCaseRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        CaseReviewRepository $caseReviewRepository,
        CriminalCaseRepository $criminalCaseRepository,
        UserRepository $userRepository,
        ReasonsForClosureRepository $reasonsForClosureRepository,
        CaseReallocationRepository $caseReallocationRepository
    )
    {
        $this->caseReviewRepository = $caseReviewRepository; // Initialize new repository
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->userRepository = $userRepository;
        $this->reasonsForClosureRepository = $reasonsForClosureRepository;
        $this->caseReallocationRepository = $caseReallocationRepository;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->caseReviewRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of the case reviews.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.crime.case_reviews.index');
    }

    /**
     * Show the form for creating a new case review.
     *
     * @return Response
     */
    public function create($id)
{
    // Get the specific case by its ID
    $case = $this->criminalCaseRepository->getById($id); // Assuming this returns a single case object

    // Retrieve reasons for closure
    $reasonsForClosure = $this->reasonsForClosureRepository->pluck();

    // Retrieve councils (if applicable)
    $councils = $this->userRepository->pluck(); // Assuming these are the councils available

    // Pass the case data to the view
    return view('oag.crime.case_reviews.create')
        ->with('case', $case) // Passing the specific case
        ->with('reasonsForClosure', $reasonsForClosure)
        ->with('councils', $councils);
}


    /**
     * Store a newly created case review in storage.
     *
     * @param Request $request
     * @return Response
     */
    /**
 * Store a newly created case review in storage.
 *
 * @param Request $request
 * @return Response
 */
public function store(Request $request)
{
    $caseId = $request->input('case_id');
    
    // Debug the entire request
    Log::info("Full request:", $request->all());

    // Validation rules with proper conditional requirements
    $rules = [
        'case_id' => 'required|exists:cases,id',
        'action_type' => 'required|in:review,reallocate,update_court_info',
        'evidence_status' => 'required|in:pending_review,sufficient_evidence,insufficient_evidence,returned_to_police',
        'review_notes' => 'required|string',
        'review_date' => 'required|date',
        
        // Only require reason_for_closure_id when evidence status indicates closure
        'reason_for_closure_id' => 'required_if:evidence_status,insufficient_evidence,returned_to_police|exists:reasons_for_closure,id|nullable',
        
        // Only require these when action type is reallocate
        'new_lawyer_id' => 'required_if:action_type,reallocate|exists:users,id|nullable',
        'reallocation_reason' => 'required_if:action_type,reallocate|string|nullable',
        
        // Only require this when action type is update_court_info
        'court_case_number' => 'required_if:action_type,update_court_info|string|nullable',
    ];
    
    // Validate with custom messages for clearer errors
    $customMessages = [
        'new_lawyer_id.required_if' => 'Please select a lawyer to reallocate the case to.',
        'reallocation_reason.required_if' => 'Please provide a reason for the reallocation.',
        'court_case_number.required_if' => 'Please enter the court case number.',
        'reason_for_closure_id.required_if' => 'Please select a reason for closing the case.'
    ];
    
    $data = $request->validate($rules, $customMessages);

    // Add the authenticated user ID
    $data['created_by'] = auth()->id();

    // Store the case review
    $caseReview = $this->caseReviewRepository->create($data);
    Log::info('Created Case Review:', ['caseReview' => $caseReview]);

    // Handle different action types
    try {
        switch ($data['action_type']) {
            case 'reallocate':
                $this->handleCaseReallocation($data);
                break;

            case 'update_court_info':
                $this->handleCourtInfoUpdate($data);
                break;
        }

        // Handle case closure/reopening based on evidence status
        $this->handleCaseStatusUpdate($data);

        return redirect()->route('crime.CaseReview.index')
            ->with('caseid', $caseId)
            ->with('success', 'Case review created successfully.');
            
    } catch (\Exception $e) {
        Log::error("Error processing case review: " . $e->getMessage());
        return redirect()->route('crime.CaseReview.index')
            ->with('error', 'There was an error processing your request: ' . $e->getMessage());
    }
}

/**
 * Handle case reallocation
 */
private function handleCaseReallocation(array $data)
{
    Log::info("Reallocate Case: Case ID {$data['case_id']} to Lawyer ID {$data['new_lawyer_id']}");
    
    // Get the current case to find out the current lawyer
    $currentCase = $this->criminalCaseRepository->getById($data['case_id']);
    
    // Reallocate the case to a new lawyer
    $this->criminalCaseRepository->update($data['case_id'], [
        'lawyer_id' => $data['new_lawyer_id'],
        'updated_by' => auth()->id(),
    ]);
    
    // Log the reallocation in a separate history table
    $this->caseReallocationRepository->create([
        'case_id' => $data['case_id'],
        'from_lawyer_id' => $currentCase->lawyer_id,
        'to_lawyer_id' => $data['new_lawyer_id'],
        'reallocation_reason' => $data['reallocation_reason'],
        'reallocation_date' => now(),
        'created_by' => auth()->id(),
        // Remove updated_by if column doesn't exist in database
        // 'updated_by' => auth()->id(),
    ]);
}

/**
 * Handle court information update
 */
private function handleCourtInfoUpdate(array $data)
{
    Log::info("Update Court Info: Case ID {$data['case_id']} with Court Case Number {$data['court_case_number']}");
    
    if (empty($data['court_case_number'])) {
        throw new \Exception("Court case number is required");
    }
    
    $this->criminalCaseRepository->update($data['case_id'], [
        'court_case_number' => $data['court_case_number'],
        'updated_by' => auth()->id(),
    ]);
}

/**
 * Handle case status updates based on evidence status
 */
private function handleCaseStatusUpdate(array $data)
{
    if (in_array($data['evidence_status'], ['insufficient_evidence', 'returned_to_police'])) {
        // Close the case
        Log::info("Case Closure: Updating case ID {$data['case_id']} with closure status.");
        
        $this->criminalCaseRepository->update($data['case_id'], [
            'date_file_closed' => now()->format('Y-m-d'),
            'reason_for_closure_id' => $data['reason_for_closure_id'],
            'updated_by' => auth()->id(),
        ]);
    } elseif ($data['evidence_status'] == 'sufficient_evidence') {
        // Reopen the case
        Log::info("Reopen Case: Case ID {$data['case_id']} with sufficient evidence.");
        
        $this->criminalCaseRepository->update($data['case_id'], [
            'date_file_closed' => null,
            'reason_for_closure_id' => null,
            'updated_by' => auth()->id(),
        ]);
    }
}
    /**
     * Display the specified case review.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $caseReview = $this->caseReviewRepository->getById($id);

        if (!$caseReview) {
            return response()->json(['message' => 'Case review not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.crime.case_reviews.show')->with('caseReview', $caseReview);
    }

    /**
     * Show the form for editing the specified case review.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $caseReview = $this->caseReviewRepository->getById($id);

        if (!$caseReview) {
            return redirect()->route('crime.case_reviews.index')->with('error', 'Case review not found.');
        }

        $cases = $this->criminalCaseRepository->pluck();
        $lawyers = $this->userRepository->pluck();

        return view('oag.crime.case_reviews.edit')
            ->with('caseReview', $caseReview)
            ->with('cases', $cases)
            ->with('lawyers', $lawyers);
    }

    /**
 * Update the specified case review in storage.
 *
 * @param Request $request
 * @param int $id
 * @return Response
 */
public function update(Request $request, $id)
{
    // Validate request data
    $data = $request->validate([
        'case_id' => 'required|exists:cases,id',
        'lawyer_id' => 'required|exists:users,id',
        'evidence_status' => 'required|in:pending_review,sufficient_evidence,insufficient_evidence,returned_to_police',
        'review_notes' => 'required|string',
        'review_date' => 'required|date',
        'reason_for_closure_id' => 'required_if:evidence_status,insufficient_evidence,returned_to_police|exists:reasons_for_closure,id|nullable',
    ]);
    
    // Add the authenticated user as 'updated_by'
    $data['updated_by'] = auth()->id();
    
    // Get the current review to check if status has changed
    $currentReview = $this->caseReviewRepository->getById($id);
    $newStatus = $data['evidence_status'];
    
    // Attempt to update the case review
    $updated = $this->caseReviewRepository->update($id, $data);
    
    if (!$updated) {
        return response()->json(['message' => 'Case review not found or failed to update'], Response::HTTP_NOT_FOUND);
    }
    
    // Handle case closure/reopening based on evidence status change
    if ($currentReview && $currentReview->evidence_status != $newStatus) {
        if (in_array($newStatus, ['insufficient_evidence', 'returned_to_police'])) {
            // Close the case
            $caseUpdateData = [
                'date_file_closed' => now()->format('Y-m-d'),
                'reason_for_closure_id' => $data['reason_for_closure_id'],
                'updated_by' => auth()->id()
            ];
            
            $this->criminalCaseRepository->update($data['case_id'], $caseUpdateData);
        } 
        elseif ($newStatus == 'sufficient_evidence' && 
                in_array($currentReview->evidence_status, ['insufficient_evidence', 'returned_to_police'])) {
            // Reopen the case if it was previously closed due to insufficient evidence
            $caseUpdateData = [
                'date_file_closed' => null,
                'reason_for_closure_id' => null,
                'updated_by' => auth()->id()
            ];
            
            $this->criminalCaseRepository->update($data['case_id'], $caseUpdateData);
        }
    }
    
    return redirect()->route('crime.case_reviews.index')->with('success', 'Case review updated successfully.');
}

    /**
     * Remove the specified case review from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->caseReviewRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Case review not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Case review deleted successfully']);
    }
}
