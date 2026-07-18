<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\AgReviewRepository;
use App\Repositories\Oag\Crime\CaseReviewRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use Illuminate\Http\Request;
use DataTables;

class AgReviewController extends Controller
{
    use AuthorizesCriminalCase;

    protected $agReviewRepository;
    protected $caseReviewRepository;
    protected $criminalCaseRepository;

    public function __construct(
        AgReviewRepository $agReviewRepository,
        CaseReviewRepository $caseReviewRepository,
        CriminalCaseRepository $criminalCaseRepository
    ) {
        $this->agReviewRepository = $agReviewRepository;
        $this->caseReviewRepository = $caseReviewRepository;
        $this->criminalCaseRepository = $criminalCaseRepository;
    }

    public function index()
    {
        return view('oag.crime.ag_reviews.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->agReviewRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * A case is only ready for AG submission once its Case Review found
     * sufficient evidence — mirrors the "Case Review" gate on Court Case.
     */
    private function assertReadyForAgReview($case): void
    {
        $review = $this->caseReviewRepository->getReviewsByCaseId($case->id)->first();
        abort_unless(
            $review && $review->evidence_status === 'sufficient_evidence',
            403,
            'This case must have a Case Review with sufficient evidence before it can be submitted to the AG.'
        );

        abort_if(
            $this->agReviewRepository->hasActiveSubmission($case->id),
            403,
            'This case already has an AG review pending or approved.'
        );
    }

    public function create($id)
    {
        abort_unless(auth()->user()->hasRole('cm.user'), 403);

        $case = $this->criminalCaseRepository->getById($id);
        abort_if(!$case, 404);
        $this->assertCanActOnCase($case, auth()->user());
        $this->assertCaseIsActionable($case);
        $this->assertReadyForAgReview($case);

        return view('oag.crime.ag_reviews.create', compact('case'));
    }

    public function store(\App\Http\Requests\Oag\Crime\AgReviewStoreRequest $request)
    {
        $case = $this->criminalCaseRepository->getById($request->input('case_id'));
        abort_if(!$case, 404);

        $data = $request->validated();

        $data['submitted_by'] = auth()->id();
        $data['ag_decision'] = 'pending';
        $data['created_by'] = auth()->id();
        $data['updated_by'] = null;

        $this->agReviewRepository->create($data);

        return redirect()->route('crime.criminalCase.index')
            ->with('success', 'Case submitted to the AG for review.');
    }

    public function show($id)
    {
        $agReview = $this->agReviewRepository->getById($id);
        abort_if(!$agReview, 404);

        return view('oag.crime.ag_reviews.show', compact('agReview'));
    }

    public function edit($id)
    {
        abort_unless(auth()->user()->hasRole('cm.ag'), 403);

        $agReview = $this->agReviewRepository->getById($id);
        abort_if(!$agReview, 404);

        // The AG previously only saw the case name and submission date —
        // nothing about the actual legal work — so there was no way to
        // evaluate the case without leaving the page. Load everything
        // needed to make the approve/reject call here instead.
        $agReview->load(['case.accused', 'case.victims', 'case.offences', 'case.island', 'case.lawyer']);

        $caseReview = $this->caseReviewRepository->getReviewsByCaseId($agReview->case_id)->first();

        // If this is a resubmission after a rejection, show that history so
        // the AG can check whether their earlier concerns were addressed.
        $priorSubmissions = $this->agReviewRepository->getPriorSubmissions($agReview->case_id, $agReview->id);

        return view('oag.crime.ag_reviews.edit', compact('agReview', 'caseReview', 'priorSubmissions'));
    }

    public function update(\App\Http\Requests\Oag\Crime\AgReviewUpdateRequest $request, $id)
    {
        $agReview = $this->agReviewRepository->getById($id);
        abort_if(!$agReview, 404);

        $data = $request->validated();

        $data['updated_by'] = auth()->id();

        $this->agReviewRepository->update($id, $data);

        // A rejection is not a dead end — the case stays 'accepted' so the
        // same lawyer can revise their Case Review / evidence and resubmit
        // (a fresh ag_reviews row), matching the "revision loop" the AG
        // review step is designed around. Approval needs no case-status
        // change here; Registry Dispatch reads the latest approved decision.

        return redirect()->route('crime.ag-reviews.index')
            ->with('success', 'AG decision recorded.');
    }

    public function destroy($id)
    {
        $deleted = $this->agReviewRepository->deleteById($id);

        if (!$deleted) {
            return redirect()->route('crime.ag-reviews.index')
                ->with('error', 'AG review not found or could not be deleted.');
        }

        return redirect()->route('crime.ag-reviews.index')
            ->with('success', 'AG review deleted successfully.');
    }
}
