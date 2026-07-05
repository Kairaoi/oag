<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\CaseReviewRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\UserRepository;
use App\Repositories\Oag\Crime\ReasonsForClosureRepository;
use App\Repositories\Oag\Crime\OffenceRepository;
use App\Repositories\Oag\Crime\OffenceCategoryRepository;
use App\Repositories\Oag\Crime\CaseReallocationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;
use Illuminate\Support\Facades\Log;

class CaseReviewController extends Controller
{
    use AuthorizesCriminalCase;

    protected $caseReviewRepository;
    protected $criminalCaseRepository;
    protected $userRepository;
    protected $reasonsForClosureRepository;
    protected $offenceRepository;
    protected $offenceCategoryRepository;
    protected $caseReallocationRepository;

    public function __construct(
        CaseReviewRepository $caseReviewRepository,
        CriminalCaseRepository $criminalCaseRepository,
        UserRepository $userRepository,
        ReasonsForClosureRepository $reasonsForClosureRepository,
        OffenceRepository $offenceRepository,
        OffenceCategoryRepository $offenceCategoryRepository,
        CaseReallocationRepository $caseReallocationRepository
    ) {
        $this->caseReviewRepository = $caseReviewRepository;
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->userRepository = $userRepository;
        $this->reasonsForClosureRepository = $reasonsForClosureRepository;
        $this->offenceRepository = $offenceRepository;
        $this->offenceCategoryRepository = $offenceCategoryRepository;
        $this->caseReallocationRepository = $caseReallocationRepository;
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
        abort_unless(auth()->user()->hasRole('cm.user'), 403);

        $case = $this->criminalCaseRepository->getById($id);
        abort_if(!$case, 404);
        $this->assertCanActOnCase($case, auth()->user());
        abort_unless($case->status === 'accepted', 403, 'This case must be accepted before it can be reviewed.');

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
        abort_unless(auth()->user()->hasRole('cm.user'), 403);

        // Retrieve the case ID from the request
        $caseId = $request->input('case_id');
        $case = $this->criminalCaseRepository->getById($caseId);
        abort_if(!$case, 404);
        $this->assertCanActOnCase($case, auth()->user());
        abort_unless($case->status === 'accepted', 403, 'This case must be accepted before it can be reviewed.');
        $this->assertCaseIsActionable($case);

        Log::info("Full request:", $request->all());
    
        // Define validation rules
        $rules = [
            'case_id' => 'required|exists:cases,id',
            'evidence_status' => 'required|in:pending_review,sufficient_evidence,insufficient_evidence,returned_to_police',
           
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
    
            return redirect()->route('crime.criminalCase.index')
                ->with('caseid', $caseId)
                ->with('success', 'Case review created successfully.');
    
        } catch (\Exception $e) {
            Log::error("Error processing case review: " . $e->getMessage());
            return redirect()->route('crime.criminalCase.index')
                ->with('error', 'There was an error processing your request: ' . $e->getMessage());
        }
    }
    

    
    private function handleCaseStatusUpdate(array $data)
    {
        // Initialize case status
        $caseStatus = null;
    
        // Determine case status based on evidence status
        if (in_array($data['evidence_status'], ['insufficient_evidence', 'returned_to_police'])) {
            $caseStatus = 'closed';
            Log::info("Case Closure Triggered: Evidence marked as '{$data['evidence_status']}'. Case ID {$data['case_id']} will be closed.");
            
            // Assumes that reason_for_closure_id and date_file_closed are handled during the review creation
        } elseif ($data['evidence_status'] === 'sufficient_evidence') {
            $caseStatus = 'accepted';
            Log::info("Case Processing: Sufficient evidence found. Case ID {$data['case_id']} marked as 'accepted'.");
        } else {
            Log::warning("Unknown Evidence Status: '{$data['evidence_status']}' provided for Case ID {$data['case_id']}. No status update performed.");
        }
    
        // Update the case status only if it's determined
        if ($caseStatus !== null) {
            $updateResult = $this->criminalCaseRepository->update($data['case_id'], [
                'status' => $caseStatus,
                'updated_by' => auth()->id(),
            ]);
    
            Log::info("Case Update Success: Case ID {$data['case_id']} status updated to '{$caseStatus}'.", ['result' => $updateResult]);
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
        // $id is the case_review's own primary key (this is a resource route),
        // so the case must be looked up via the review's case_id, not $id itself.
        $review = $this->caseReviewRepository->getById($id);
        $case = $review ? $this->criminalCaseRepository->getById($review->case_id) : null;
        $case?->load('offences');

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
        Log::info('CaseReview update request received', [
            'review_id' => $id,
            'user_id' => auth()->id(),
            'roles' => auth()->user()->roles->pluck('name'),
            'request' => $request->except(['_token']),
        ]);

        try {
            $validated = $request->validate([
                'case_id' => 'required|exists:cases,id',
                'evidence_status' => 'required|in:pending_review,sufficient_evidence,insufficient_evidence,returned_to_police',

                'review_date' => 'required|date',
                'reason_for_closure_id' => 'required_if:evidence_status,insufficient_evidence,returned_to_police|exists:reasons_for_closure,id|nullable',
                'offence_id.*' => 'required_if:evidence_status,sufficient_evidence|nullable|exists:offences,id',
                'category_id.*' => 'required_if:evidence_status,sufficient_evidence|nullable|exists:offence_categories,id',
                'action_type' => 'nullable|in:review,reallocate,update_court_info',
                'new_lawyer_id' => ['required_if:action_type,reallocate', 'nullable', 'exists:users,id', new \App\Rules\UserHasRole('cm.user')],
                'reallocation_reason' => 'required_if:action_type,reallocate|nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // The edit form has no @error/error-summary markup for
            // new_lawyer_id or reallocation_reason, so a validation failure
            // on either of those fields previously looked like the button
            // silently did nothing — logged here so a failed reallocation
            // attempt is visible even though the UI won't show it.
            Log::warning('CaseReview update validation failed', [
                'review_id' => $id,
                'action_type' => $request->input('action_type'),
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        $actionType = $validated['action_type'] ?? 'review';

        // Reallocation is an admin-level action everywhere else in the app
        // (CriminalCaseController::allocateLawyer/reallocateCase both require
        // cm.admin) — enforced here too, before any side effects, so a
        // lawyer can't use this form to move a case to themselves or
        // someone else without approval.
        if ($actionType === 'reallocate') {
            if (!auth()->user()->hasRole('cm.admin')) {
                Log::warning('CaseReview reallocation blocked: user lacks cm.admin role', [
                    'review_id' => $id,
                    'user_id' => auth()->id(),
                    'roles' => auth()->user()->roles->pluck('name'),
                ]);
            }
            abort_unless(auth()->user()->hasRole('cm.admin'), 403, 'Only an administrator can reallocate a case.');
        }

        // action_type and reallocation_reason are form-only concepts with no
        // matching column on case_reviews (only new_lawyer_id is a real
        // column there) — only real columns are passed through so mass
        // assignment doesn't throw "unknown column".
        $data = [
            'case_id' => $validated['case_id'],
            'evidence_status' => $validated['evidence_status'],
            'review_date' => $validated['review_date'],
            'reason_for_closure_id' => $validated['reason_for_closure_id'] ?? null,
            'updated_by' => auth()->id(),
        ];

        if ($actionType === 'reallocate') {
            $data['new_lawyer_id'] = $validated['new_lawyer_id'];
        }

        $currentReview = $this->caseReviewRepository->getById($id);
        $newStatus = $data['evidence_status'];
        $updated = $this->caseReviewRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Case review not found or failed to update'], Response::HTTP_NOT_FOUND);
        }

        // Offences are recorded against the case (not the individual review),
        // so a case can accumulate multiple charged offences across reviews.
        // sync() (not syncWithoutDetaching) here so unchecking/removing a row
        // in the edit form actually detaches it, reflecting the full current
        // state of what's charged rather than only ever adding.
        if ($newStatus === 'sufficient_evidence') {
            $case = $this->criminalCaseRepository->getById($data['case_id']);
            if ($case) {
                $offenceIds = $request->input('offence_id', []);
                $categoryIds = $request->input('category_id', []);
                $syncData = [];

                foreach ($offenceIds as $index => $offenceId) {
                    if ($offenceId) {
                        $syncData[$offenceId] = [
                            'category_id' => $categoryIds[$index] ?? null,
                        ];
                    }
                }

                $case->offences()->sync($syncData);
                Log::info("Synced offences for case ID {$data['case_id']}", ['syncData' => $syncData]);
            }
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

        if ($actionType === 'reallocate') {
            $case = $this->criminalCaseRepository->getById($validated['case_id']);
            abort_if(!$case, 404);

            Log::info('CaseReview reallocation starting', [
                'review_id' => $id,
                'case_id' => $case->id,
                'from_lawyer_id' => $case->lawyer_id,
                'to_lawyer_id' => $validated['new_lawyer_id'],
                'reallocation_reason' => $validated['reallocation_reason'],
            ]);

            $reallocation = $this->caseReallocationRepository->create([
                'case_id' => $case->id,
                'from_lawyer_id' => $case->lawyer_id,
                'to_lawyer_id' => $validated['new_lawyer_id'],
                'reallocation_reason' => $validated['reallocation_reason'],
                'reallocation_date' => now()->format('Y-m-d'),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            Log::info('CaseReview reallocation record created', ['case_reallocation_id' => $reallocation->id ?? null]);

            $this->criminalCaseRepository->update($case->id, [
                'lawyer_id' => $validated['new_lawyer_id'],
                'status' => 'reallocated',
                'updated_by' => auth()->id(),
            ]);

            Log::info("Case {$case->id} reallocated via case review edit", [
                'from_lawyer_id' => $case->lawyer_id,
                'to_lawyer_id' => $validated['new_lawyer_id'],
            ]);

            return redirect()->route('crime.CaseReview.index')->with('success', 'Case review updated and case reallocated successfully.');
        }

        return redirect()->route('crime.CaseReview.index')->with('success', 'Case review updated successfully.');
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