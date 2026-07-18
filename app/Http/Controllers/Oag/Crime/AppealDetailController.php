<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use Illuminate\Http\Request;
use App\Repositories\Oag\Crime\AppealDetailRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\CourtRepository;
use App\Repositories\Oag\Crime\IslandRepository;
use App\Repositories\Oag\Crime\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use DataTables;

class AppealDetailController extends Controller
{
    use AuthorizesCriminalCase;

    protected $appealDetailRepository;
    protected $criminalCaseRepository;
    protected $courtRepository;
    protected $islandRepository;
    protected $userRepository;

    public function __construct(UserRepository $userRepository,IslandRepository $islandRepository,        AppealDetailRepository $appealDetailRepository,
        CriminalCaseRepository $criminalCaseRepository,CourtRepository $courtRepository
    ) {
        $this->appealDetailRepository = $appealDetailRepository;
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->courtRepository = $courtRepository;
        $this->islandRepository = $islandRepository;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        return view('oag.crime.appeal_details.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->appealDetailRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    public function create($id = null)
    {
        abort_unless(auth()->user()->hasRole('cm.user'), 403);

        if ($id) {
            $originalCase = $this->criminalCaseRepository->getById($id);

            if (!$originalCase || $originalCase->is_appeal_case || $originalCase->is_on_appeal) {
                return redirect()->route('crime.criminalCase.index')->with('error', 'Te case ae rineaki e aki tau ibukin appeal.');
            }

            $this->assertCaseIsActionable($originalCase);

            $cases = [$originalCase->id => $originalCase->case_name];

            $suggestedValues = [
                'case_name' => "Appeal - {$originalCase->case_name}",
                'island_id' => $originalCase->island_id,
                'lawyer_id' => $originalCase->lawyer_id,
            ];
        } else {
            $cases = $this->criminalCaseRepository->getNonAppealCases();
            $suggestedValues = [];
        }

        $courtsOfAppeal = $this->courtRepository->pluck();
        $islands = $this->islandRepository->pluck();

        return view('oag.crime.appeal_details.create')
            ->with('cases', $cases)
            ->with('courtsOfAppeal', $courtsOfAppeal)
            ->with('islands', $islands)
            ->with('selectedCaseId', $id)
            ->with('suggestedValues', $suggestedValues);
    }




    public function store(\App\Http\Requests\Oag\Crime\AppealDetailStoreRequest $request, AppealDetailRepository $appealRepo)
{
    $originalCaseForAuth = $this->criminalCaseRepository->getById($request->input('case_id'));
    abort_if(!$originalCaseForAuth, 404);

    \Log::info('Incoming Appeal Detail form submission: ', $request->all());

    $validatedData = $request->validated();

    // Map validated fields to actual columns
    // Map validated fields to actual columns
$data = [
    'case_id' => $validatedData['case_id'],
    'high_court_case_number' => $validatedData['high_court_case_number'] ?? null,
    'magistrate_court_case_number' => $validatedData['magistrate_court_case_number'] ?? null,
    'appeal_case_number' => $validatedData['appeal_case_number'],
    'court_outcome' => $validatedData['court_outcome'],
    'judgment_delivered_date' => $validatedData['judgment_delivered_date'] ?? null,
    'appeal_status' => $validatedData['appeal_status'],
    'decision_principle_established' => $validatedData['decision_principle_established'] ?? null,
    'appeal_filing_date' => $validatedData['filing_date_value'],
    'filing_date_source' => $validatedData['filing_date_type'], // 🔧 added
    'created_by' => auth()->id(),
    'updated_by' => auth()->id(),
];


    // Store date in correct column
    if ($validatedData['filing_date_type'] === 'court') {
        $data['appeal_filing_date'] = $validatedData['filing_date_value'];
    } else {
        $data['appeal_filing_date'] = $validatedData['filing_date_value'];
        $data['appeal_filing_received_date'] = $validatedData['filing_date_value'];
    }

    try {
        $originalCase = $originalCaseForAuth;
        $originalCase->is_on_appeal = true;
        $originalCase->save();

        $appeal = $appealRepo->create($data);
        \Log::info('Appeal stored successfully', $appeal->toArray());

        return redirect()->route('crime.criminalCase.index')->with('success', 'Appeal created successfully.');
    } catch (\Exception $e) {
        \Log::error('Error storing appeal: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Failed to store appeal.');
    }
}

    

    /**
     * appeal_details/appeal.blade.php is a report-style view built around
     * a collection (it foreach-loops $appealDetails), reusing the same
     * joined shape as getAppealDetailsByCaseId() — so a single appeal is
     * wrapped in a one-item collection rather than passed as a bare model.
     */
    public function show($id)
    {
        $appeal = $this->appealDetailRepository->getAppealDetailById($id);
        abort_if(!$appeal, 404);

        $appealDetails = collect([$appeal]);

        return view('oag.crime.appeal_details.appeal', compact('appealDetails'));
    }

    public function edit($id)
    {
        $appeal = $this->appealDetailRepository->getById($id);
        return view('oag.crime.appeal_details.edit', compact('appeal'));
    }

    public function update(\App\Http\Requests\Oag\Crime\AppealDetailUpdateRequest $request, $id)
    {
        $data = $request->validated();

        $data['updated_by'] = Auth::id();

        try {
            $appeal = $this->appealDetailRepository->update($id, $data);

            // Once an appeal is resolved (dismissed/withdrawn), free up the case
            // so it's eligible for a fresh appeal again if ever needed. "Appealed"
            // means it has escalated to the Court of Appeal, not resolved — it
            // should still block a new appeal at this level, same as "pending".
            if (in_array($data['appeal_status'], ['dismissed', 'withdrawn'])) {
                $originalCase = $this->criminalCaseRepository->getById($appeal->case_id);
                if ($originalCase) {
                    $originalCase->is_on_appeal = false;
                    $originalCase->save();
                }
            }

            return redirect()->route('crime.appeal.index')->with('success', 'Appeal updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating appeal: ' . $e->getMessage());
            return back()->with('error', 'Failed to update appeal.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->appealDetailRepository->deleteById($id);
            return response()->json(['message' => 'Appeal deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete appeal.'], 500);
        }
    }
}
