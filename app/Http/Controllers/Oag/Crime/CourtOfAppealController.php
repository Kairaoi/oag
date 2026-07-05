<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use Illuminate\Http\Request;
use App\Repositories\Oag\Crime\CourtOfAppealRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CourtOfAppealController extends Controller
{
    use AuthorizesCriminalCase;

    protected $courtOfAppealRepository;
    protected $criminalCaseRepository;
    protected $userRepository;

    public function __construct(
        CourtOfAppealRepository $courtOfAppealRepository,
        CriminalCaseRepository $criminalCaseRepository,
        UserRepository $userRepository
    ) {
        $this->courtOfAppealRepository = $courtOfAppealRepository;
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $appeals = $this->courtOfAppealRepository->all();
        // dd($appeals);
        return view('oag.crime.court_of_appeals.index', compact('appeals'));
    }

    public function create($caseId = null)
    {
        abort_unless(auth()->user()->hasRole('cm.user'), 403);

        if ($caseId) {
            $case = $this->criminalCaseRepository->getById($caseId);

            if (!$case) {
                return redirect()->route('crime.criminalCase.index')->with('error', 'Criminal Case not found.');
            }

            $this->assertCaseIsActionable($case);
        }

        $cases = $this->criminalCaseRepository->pluck();
        return view('oag.crime.court_of_appeals.create', compact('cases', 'caseId'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasRole('cm.user'), 403);

        $case = $this->criminalCaseRepository->getById($request->input('case_id'));
        abort_if(!$case, 404);
        $this->assertCanActOnCase($case, auth()->user());
        $this->assertCaseIsActionable($case);

        \Log::info('Court of Appeal create request:', $request->all());

        $validated = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'appeal_case_number' => 'nullable|string',
            'appeal_filing_date' => 'required|date',
            'filing_date_source' => 'required|string',
            'judgment_delivered_date' => 'nullable|date',
            'court_outcome' => 'nullable|in:win,lose,remand',
            'decision_principle_established' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        try {
            $appeal = $this->courtOfAppealRepository->create($validated);
            Log::info('Court of Appeal created:', $appeal->toArray());

            return redirect()->route('crime.courtOfAppeal.index')->with('success', 'Court of Appeal record created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating court of appeal: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create court of appeal record.');
        }
    }

    public function show($id)
    {
        $appeal = $this->courtOfAppealRepository->getByIdWithCaseName($id);
        abort_if(!$appeal, 404);

        return view('oag.crime.court_of_appeals.show', compact('appeal'));
    }

    public function edit($id)
    {
        $appeal = $this->courtOfAppealRepository->getById($id);
        abort_if(!$appeal, 404);
        $cases = $this->criminalCaseRepository->pluck();

        return view('oag.crime.court_of_appeals.edit', compact('appeal', 'cases'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'appeal_case_number' => 'nullable|string',
            'appeal_filing_date' => 'required|date',
            'filing_date_source' => 'required|string',
            'judgment_delivered_date' => 'nullable|date',
            'court_outcome' => 'nullable|in:win,lose,remand',
            'decision_principle_established' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        try {
            $this->courtOfAppealRepository->update($id, $validated);
            return redirect()->route('crime.courtOfAppeal.index')->with('success', 'Court of Appeal record updated.');
        } catch (\Exception $e) {
            Log::error('Error updating court of appeal: ' . $e->getMessage());
            return back()->with('error', 'Failed to update record.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->courtOfAppealRepository->deleteById($id);
            return response()->json(['message' => 'Record deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete record.'], 500);
        }
    }
}
