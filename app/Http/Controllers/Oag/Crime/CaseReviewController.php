<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Crime\CaseReviewRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\UserRepository;
use App\Repositories\Oag\Crime\ReasonsForClosureRepository;
use App\Repositories\Oag\Crime\OffenceRepository;
use App\Repositories\Oag\Crime\OffenceCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;
use Illuminate\Support\Facades\Log;

class CaseReviewController extends Controller
{
    protected $caseReviewRepository;
    protected $criminalCaseRepository;
    protected $userRepository;
    protected $reasonsForClosureRepository;
    protected $offenceRepository;
    protected $offenceCategoryRepository;

    public function __construct(
        CaseReviewRepository $caseReviewRepository,
        CriminalCaseRepository $criminalCaseRepository,
        UserRepository $userRepository,
        ReasonsForClosureRepository $reasonsForClosureRepository,
        OffenceRepository $offenceRepository,
        OffenceCategoryRepository $offenceCategoryRepository
    ) {
        $this->caseReviewRepository = $caseReviewRepository;
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->userRepository = $userRepository;
        $this->reasonsForClosureRepository = $reasonsForClosureRepository;
        $this->offenceRepository = $offenceRepository;
        $this->offenceCategoryRepository = $offenceCategoryRepository;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->caseReviewRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    public function index()
    {
        return view('oag.crime.case_reviews.index');
    }

    public function create($id)
    {
        $case = $this->criminalCaseRepository->getById($id);
        $reasonsForClosure = $this->reasonsForClosureRepository->pluck();
        $councils = $this->userRepository->pluck();
        $offences = $this->offenceRepository->pluck2();
        $categories = $this->offenceCategoryRepository->pluck();

        return view('oag.crime.case_reviews.create')
            ->with('case', $case)
            ->with('reasonsForClosure', $reasonsForClosure)
            ->with('offences', $offences)
            ->with('categories', $categories)
            ->with('councils', $councils);
    }

    public function store(Request $request)
    {
        // Retrieve the case ID from the request
        $caseId = $request->input('case_id');
        Log::info("Full request:", $request->all());
    
        // Define validation rules
        $rules = [
            'case_id' => 'required|exists:cases,id',
            'evidence_status' => 'required|in:pending_review,sufficient_evidence,insufficient_evidence,returned_to_police',
            'review_notes' => 'required|string',
            'review_date' => 'required|date',
            'reason_for_closure_id' => 'required_if:evidence_status,insufficient_evidence,returned_to_police|nullable|exists:reasons_for_closure,id',
            'offence_id.*' => 'required_if:evidence_status,sufficient_evidence|nullable|exists:offences,id',
            'category_id.*' => 'required_if:evidence_status,sufficient_evidence|nullable|exists:offence_categories,id',
            'offence_particulars' => 'required_if:evidence_status,sufficient_evidence|nullable|string', // changed to single field
        ];
    
        // Custom error messages
        $customMessages = [
            'reason_for_closure_id.required_if' => 'Please select a reason for closing the case.',
        ];
    
        // Validate incoming data
        $data = $request->validate($rules, $customMessages);
        $data['created_by'] = auth()->id();
    
        // If the case is closed, set closure date
        if (in_array($data['evidence_status'], ['insufficient_evidence', 'returned_to_police'])) {
            $data['date_file_closed'] = now()->format('Y-m-d');
        }
    
        // Create the case review
        $caseReview = $this->caseReviewRepository->create($data);
        Log::info('Created Case Review:', ['caseReview' => $caseReview]);
    
        try {
            // Update case status
            $this->handleCaseStatusUpdate($data);
    
            // Handle offence syncing only for sufficient evidence
            if ($request->evidence_status === 'sufficient_evidence') {
                $case = $this->criminalCaseRepository->getById($request->case_id);
    
                if ($case) {
                    $offenceIds = $request->offence_id;
                    $categoryIds = $request->category_id;
    
                    $syncData = [];
    
                    foreach ($offenceIds as $index => $offenceId) {
                        if ($offenceId) {
                            $syncData[$offenceId] = [
                                'category_id' => $categoryIds[$index] ?? null,
                                // 'offence_particulars' => $request->offence_particulars, // ❌ Remove this line
                            ];
                        }
                    }
                    
    
                    $case->offences()->syncWithoutDetaching($syncData);
                    Log::info("Linked offences to case ID {$request->case_id}", ['syncData' => $syncData]);
                } else {
                    Log::warning("Case not found: {$request->case_id}");
                }
            }
    
            return redirect()->route('crime.CaseReview.index')
                ->with('caseid', $caseId)
                ->with('success', 'Case review created successfully.');
    
        } catch (\Exception $e) {
            Log::error("Error processing case review: " . $e->getMessage());
            return redirect()->route('crime.CaseReview.index')
                ->with('error', 'There was an error processing your request: ' . $e->getMessage());
        }
    }
    

    
    private function handleCaseStatusUpdate(array $data)
{
    // Update the case's status based on evidence status
    $caseStatus = '';
    
    if (in_array($data['evidence_status'], ['insufficient_evidence', 'returned_to_police'])) {
        // Case closed
        $caseStatus = 'closed';
        Log::info("Case Closure: Updating case ID {$data['case_id']} with closed status.");
        
        // The date_file_closed and reason_for_closure_id are already in the case_review
        // record that was created earlier via $this->caseReviewRepository->create($data);
    } 
    elseif ($data['evidence_status'] === 'sufficient_evidence') {
        // Case being processed
        $caseStatus = 'accepted';
        Log::info("Case Progress: Case ID {$data['case_id']} marked as in progress.");
    }
    
    // Only update the status field in the criminal case table
    if (!empty($caseStatus)) {
        $updateResult = $this->criminalCaseRepository->update($data['case_id'], [
            'status' => $caseStatus,
            'updated_by' => auth()->id(),
        ]);
        
        Log::info("Updated case ID {$data['case_id']} status to {$caseStatus}", ['result' => $updateResult]);
    }
}

    public function show($id)
    {
        $caseReview = $this->caseReviewRepository->getById($id);

        if (!$caseReview) {
            return response()->json(['message' => 'Case review not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.crime.case_reviews.show')->with('caseReview', $caseReview);
    }

    public function edit($id)
    {
        $case = $this->criminalCaseRepository->getById($id);
        $review = $this->caseReviewRepository->getById($id); // Assuming 1 review per case
    
        $reasonsForClosure = $this->reasonsForClosureRepository->pluck();
        $councils = $this->userRepository->pluck();
        $offences = $this->offenceRepository->pluck2();
        $categories = $this->offenceCategoryRepository->pluck();
    
        return view('oag.crime.case_reviews.edit')
        ->with('case', $case)
        ->with('caseReview', $review) // <- changed here
        ->with('reasonsForClosure', $reasonsForClosure)
        ->with('offences', $offences)
        ->with('categories', $categories)
        ->with('councils', $councils);
    
    }
    

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'lawyer_id' => 'required|exists:users,id',
            'evidence_status' => 'required|in:pending_review,sufficient_evidence,insufficient_evidence,returned_to_police',
            'review_notes' => 'required|string',
            'review_date' => 'required|date',
            'reason_for_closure_id' => 'required_if:evidence_status,insufficient_evidence,returned_to_police|exists:reasons_for_closure,id|nullable',
        ]);

        $data['updated_by'] = auth()->id();
        $currentReview = $this->caseReviewRepository->getById($id);
        $newStatus = $data['evidence_status'];
        $updated = $this->caseReviewRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Case review not found or failed to update'], Response::HTTP_NOT_FOUND);
        }

        if ($currentReview && $currentReview->evidence_status !== $newStatus) {
            if (in_array($newStatus, ['insufficient_evidence', 'returned_to_police'])) {
                $this->criminalCaseRepository->update($data['case_id'], [
                    'date_file_closed' => now()->format('Y-m-d'),
                    'reason_for_closure_id' => $data['reason_for_closure_id'],
                    'updated_by' => auth()->id()
                ]);
            } elseif (
                $newStatus === 'sufficient_evidence' &&
                in_array($currentReview->evidence_status, ['insufficient_evidence', 'returned_to_police'])
            ) {
                $this->criminalCaseRepository->update($data['case_id'], [
                    'date_file_closed' => null,
                    'reason_for_closure_id' => null,
                    'updated_by' => auth()->id()
                ]);
            }
        }

        return redirect()->route('crime.case_reviews.index')->with('success', 'Case review updated successfully.');
    }

    public function destroy($id)
    {
        $deleted = $this->caseReviewRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Case review not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Case review deleted successfully']);
    }
}
