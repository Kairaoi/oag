<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\RegistryDispatchRepository;
use App\Repositories\Oag\Crime\AgReviewRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use DataTables;

class RegistryDispatchController extends Controller
{
    use AuthorizesCriminalCase;

    protected $registryDispatchRepository;
    protected $agReviewRepository;
    protected $criminalCaseRepository;

    public function __construct(
        RegistryDispatchRepository $registryDispatchRepository,
        AgReviewRepository $agReviewRepository,
        CriminalCaseRepository $criminalCaseRepository
    ) {
        $this->registryDispatchRepository = $registryDispatchRepository;
        $this->agReviewRepository = $agReviewRepository;
        $this->criminalCaseRepository = $criminalCaseRepository;
    }

    public function index()
    {
        return view('oag.crime.registry_dispatches.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->registryDispatchRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * A case can only be dispatched once the AG has approved it, and only
     * once — a second dispatch on top of an existing one would be a
     * duplicate filing to the Registry.
     */
    private function assertReadyForDispatch($case): void
    {
        $latestAgReview = $this->agReviewRepository->getLatestForCase($case->id);
        abort_unless(
            $latestAgReview && $latestAgReview->ag_decision === 'approved',
            403,
            'This case must be approved by the AG before it can be dispatched.'
        );

        abort_if(
            $this->registryDispatchRepository->hasDispatch($case->id),
            403,
            'This case has already been dispatched.'
        );
    }

    public function create($id)
    {
        abort_unless(auth()->user()->hasRole('cm.registrar'), 403);

        $case = $this->criminalCaseRepository->getById($id);
        abort_if(!$case, 404);
        $this->assertCaseIsActionable($case);
        $this->assertReadyForDispatch($case);

        return view('oag.crime.registry_dispatches.create', compact('case'));
    }

    public function store(\App\Http\Requests\Oag\Crime\RegistryDispatchStoreRequest $request)
    {
        $case = $this->criminalCaseRepository->getById($request->input('case_id'));
        abort_if(!$case, 404);

        $data = $request->validated();

        $data['dispatched_by'] = auth()->id();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = null;

        $this->registryDispatchRepository->create($data);

        return redirect()->route('crime.criminalCase.index')
            ->with('success', 'Case dispatched to the High Court Registry.');
    }

    public function show($id)
    {
        $dispatch = $this->registryDispatchRepository->getById($id);
        abort_if(!$dispatch, 404);

        return view('oag.crime.registry_dispatches.show', compact('dispatch'));
    }

    /**
     * Printable certificate handed to (or attached to the physical file for)
     * the High Court Registry — the Court isn't a user of this system, so
     * this is the paper-based equivalent of "giving them access" to the
     * dispatch record.
     */
    public function certificate($id)
    {
        $dispatch = $this->registryDispatchRepository->getById($id);
        abort_if(!$dispatch, 404);

        $dispatch->load(['case.island', 'case.lawyer', 'dispatchedBy']);
        $agReview = $this->agReviewRepository->getLatestForCase($dispatch->case_id);

        $shareUrlExpiresAt = now()->addDays(30);
        $shareUrl = URL::temporarySignedRoute(
            'crime.registry-dispatches.public-certificate',
            $shareUrlExpiresAt,
            ['id' => $dispatch->id]
        );

        return view('oag.crime.registry_dispatches.certificate', compact('dispatch', 'agReview', 'shareUrl', 'shareUrlExpiresAt'));
    }

    /**
     * Unauthenticated counterpart to certificate() — reachable only with a
     * valid signature (see the "signed" middleware on its route), so the
     * High Court Registry (not a user of this system) can open a specific
     * dispatch certificate from a link the Registrar shares with them,
     * without an OAG account. No session, no access to anything else here.
     */
    public function publicCertificate($id)
    {
        $dispatch = $this->registryDispatchRepository->getById($id);
        abort_if(!$dispatch, 404);

        $dispatch->load(['case.island', 'case.lawyer', 'dispatchedBy']);
        $agReview = $this->agReviewRepository->getLatestForCase($dispatch->case_id);

        return view('oag.crime.registry_dispatches.certificate_public', compact('dispatch', 'agReview'));
    }

    public function destroy($id)
    {
        abort_unless(auth()->user()->hasRole('cm.registrar'), 403);

        $deleted = $this->registryDispatchRepository->deleteById($id);

        if (!$deleted) {
            return redirect()->route('crime.registry-dispatches.index')
                ->with('error', 'Registry dispatch not found or could not be deleted.');
        }

        return redirect()->route('crime.registry-dispatches.index')
            ->with('success', 'Registry dispatch record deleted successfully.');
    }
}
