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
        $caseId = $request->input('case_id');
        Log::info("Full request:", $request->all());

        $rules = [
            'case_id' => 'required|exists:cases,id',
            'evidence_status' => 'required|in:pending_review,sufficient_evidence,insufficient_evidence,returned_to_police',
            'review_notes' => 'required|string',
            'review_date' => 'required|date',
            'reason_for_closure_id' => 'required_if:evidence_status,insufficient_evidence,returned_to_police|exists:reasons_for_closure,id|nullable',
            'offence_id' => 'required_if:evidence_status,sufficient_evidence|exists:offences,id|nullable',
            'category_id' => 'required_if:evidence_status,sufficient_evidence|exists:offence_categories,id|nullable',
            'offence_particulars' => 'required_if:evidence_status,sufficient_evidence|string|nullable',
        ];

        $customMessages = [
            'reason_for_closure_id.required_if' => 'Please select a reason for closing the case.'
        ];

        $data = $request->validate($rules, $customMessages);
        $data['created_by'] = auth()->id();

        $caseReview = $this->caseReviewRepository->create($data);
        Log::info('Created Case Review:', ['caseReview' => $caseReview]);

        try {
            $this->handleCaseStatusUpdate($data);

            // ✅ NEW: Update a single offence's category and particulars
            if ($request->evidence_status === 'sufficient_evidence') {
                $offence = $this->offenceRepository->getById($request->offence_id);

                if ($offence && $offence->case_id == $request->case_id) {
                    $offence->update([
                        'offence_category_id' => $request->category_id,
                        'offence_particulars' => $request->offence_particulars,
                    ]);
                    Log::info("Updated offence ID {$offence->id} with category and particulars.");
                } else {
                    Log::warning("Invalid offence ID {$request->offence_id} for case ID {$request->case_id}");
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
        if (in_array($data['evidence_status'], ['insufficient_evidence', 'returned_to_police'])) {
            Log::info("Case Closure: Updating case ID {$data['case_id']} with closure status.");

            $this->criminalCaseRepository->update($data['case_id'], [
                'date_file_closed' => now()->format('Y-m-d'),
                'reason_for_closure_id' => $data['reason_for_closure_id'],
                'updated_by' => auth()->id(),
            ]);
        } elseif ($data['evidence_status'] === 'sufficient_evidence') {
            Log::info("Reopen Case: Case ID {$data['case_id']} with sufficient evidence.");

            $this->criminalCaseRepository->update($data['case_id'], [
                'date_file_closed' => null,
                'reason_for_closure_id' => null,
                'updated_by' => auth()->id(),
            ]);
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
