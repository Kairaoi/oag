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

        // case_reviews.case_id is unique — a case only ever has one review
        // row, updated over time (e.g. after "returned to police" reopens
        // the case for re-review). If one already exists, edit it instead of
        // landing on the create form, which would violate that constraint.
        $existingReview = $this->caseReviewRepository->getReviewsByCaseId($id)->first();
        if ($existingReview) {
            return redirect()->route('crime.CaseReview.edit', $existingReview->id);
        }

        $reasonsForClosure = $this->reasonsForClosureRepository->pluck();
        $councils = $this->userRepository->pluck();
        $offences = $this->offenceRepository->pluck2();
        $categories = $this->offenceCategoryRepository->pluck();
        $offenceCategoryMap = $this->offenceRepository->categoryMap();

        return view('oag.crime.case_reviews.create')
            ->with('case', $case)
            ->with('reasonsForClosure', $reasonsForClosure)
            ->with('offences', $offences)
            ->with('categories', $categories)
            ->with('offenceCategoryMap', $offenceCategoryMap)
            ->with('councils', $councils);
    }

    public function store(\App\Http\Requests\Oag\Crime\CaseReviewStoreRequest $request)
    {
        // Retrieve the case ID from the request
        $caseId = $request->input('case_id');
        $case = $this->criminalCaseRepository->getById($caseId);
        abort_if(!$case, 404);

        // Defensive duplicate: case_reviews.case_id is unique, so a second
        // insert for the same case (e.g. a stale create form still open in
        // another tab) would otherwise fail with a raw SQL constraint error.
        $existingReview = $this->caseReviewRepository->getReviewsByCaseId($caseId)->first();
        if ($existingReview) {
            return redirect()->route('crime.CaseReview.edit', $existingReview->id)
                ->with('error', 'This case already has a review — please edit it instead.');
        }

        Log::info("Full request:", $request->all());

        $data = $request->validated();
        $data['created_by'] = auth()->id();
    
        // Only an actual closure ("insufficient evidence") stamps date_file_closed.
        // "returned_to_police" sends the file back to the lawyer's queue instead
        // of closing the case (see handleCaseStatusUpdate()), so it must not be
        // marked as closed here.
        if ($data['evidence_status'] === 'insufficient_evidence') {
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
                    $syncData = $this->buildOffenceSyncData($request->input('offences', []));
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
        if ($data['evidence_status'] === 'insufficient_evidence') {
            $caseStatus = 'closed';
            Log::info("Case Closure Triggered: insufficient evidence. Case ID {$data['case_id']} will be closed.");

            // Assumes that reason_for_closure_id and date_file_closed are handled during the review creation
        } elseif ($data['evidence_status'] === 'returned_to_police') {
            // Not a closure — the file is sent back to Police for further
            // action and reopens on the same lawyer's queue once resubmitted,
            // rather than ending the case.
            $caseStatus = 'accepted';
            Log::info("Case Returned for Further Action: Case ID {$data['case_id']} reopened as 'accepted' pending resubmission.");
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
    

    /**
     * Turn the "Offences Charged" rows (free-text offence name + category +
     * domestic violence checkbox) into case_offence pivot sync data. Each
     * typed name is resolved to a catalog offences row (created if it's
     * genuinely new) so the rest of the app — DataTables, the Case Proof
     * timeline, AG review context — can keep joining case_offence to
     * offences the way it already does.
     */
    private function buildOffenceSyncData(array $rows): array
    {
        $syncData = [];

        foreach ($rows as $row) {
            $offenceName = trim($row['offence_name'] ?? '');
            $categoryId = $row['category_id'] ?? null;

            if ($offenceName === '' || !$categoryId) {
                continue;
            }

            $offence = $this->offenceRepository->findOrCreateByName($offenceName, (int) $categoryId, auth()->id());

            $syncData[$offence->id] = [
                'category_id' => $categoryId,
                'is_domestic_violence' => !empty($row['domestic_violence']),
            ];
        }

        return $syncData;
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
        $offenceCategoryMap = $this->offenceRepository->categoryMap();

        return view('oag.crime.case_reviews.edit')
        ->with('case', $case)
        ->with('caseReview', $review) // <- changed here
        ->with('reasonsForClosure', $reasonsForClosure)
        ->with('offences', $offences)
        ->with('categories', $categories)
        ->with('offenceCategoryMap', $offenceCategoryMap)
        ->with('councils', $councils);
    
    }
    

    public function update(\App\Http\Requests\Oag\Crime\CaseReviewUpdateRequest $request, $id)
    {
        Log::info('CaseReview update request received', [
            'review_id' => $id,
            'user_id' => auth()->id(),
            'roles' => auth()->user()->roles->pluck('name'),
            'request' => $request->except(['_token']),
        ]);

        $validated = $request->validated();
        $actionType = $validated['action_type'] ?? 'review';

        // action_type and reallocation_reason are form-only concepts with no
        // matching column on case_reviews (only new_lawyer_id is a real
        // column there) — only real columns are passed through so mass
        // assignment doesn't throw "unknown column".
        $data = [
            'case_id' => $validated['case_id'],
            'evidence_status' => $validated['evidence_status'],
            'review_date' => $validated['review_date'],
            'reason_for_closure_id' => $validated['reason_for_closure_id'] ?? null,
            'closure_decision' => $validated['evidence_status'] === 'insufficient_evidence'
                ? ($validated['closure_decision'] ?? null)
                : null,
            // Only an actual closure (insufficient evidence) stamps the
            // review's own date_file_closed — mirrors store()'s behavior,
            // which this update() path previously didn't replicate.
            'date_file_closed' => $validated['evidence_status'] === 'insufficient_evidence'
                ? now()->format('Y-m-d')
                : null,
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
                $syncData = $this->buildOffenceSyncData($request->input('offences', []));
                $case->offences()->sync($syncData);
                Log::info("Synced offences for case ID {$data['case_id']}", ['syncData' => $syncData]);
            }
        }

        if ($currentReview && $currentReview->evidence_status !== $newStatus) {
            // Note: `cases` has no date_file_closed/reason_for_closure_id
            // columns of its own — that data lives only on this case_reviews
            // row (already saved above via $data). Only `status` is a real
            // column on `cases` here.
            if ($newStatus === 'insufficient_evidence') {
                // An actual closure — flip the case to 'closed'.
                $this->criminalCaseRepository->update($data['case_id'], [
                    'status' => 'closed',
                    'updated_by' => auth()->id()
                ]);
            } elseif ($newStatus === 'returned_to_police') {
                // Not a closure — send the file back to the lawyer's queue
                // instead of closing the case.
                $this->criminalCaseRepository->update($data['case_id'], [
                    'status' => 'accepted',
                    'updated_by' => auth()->id()
                ]);
            } elseif (
                $newStatus === 'sufficient_evidence' &&
                in_array($currentReview->evidence_status, ['insufficient_evidence', 'returned_to_police'])
            ) {
                $this->criminalCaseRepository->update($data['case_id'], [
                    'status' => 'accepted',
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