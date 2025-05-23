<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Oag\Crime\AppealDetailRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\OAG\Crime\CourtRepository;
use App\Repositories\Oag\Crime\IslandRepository;
use App\Repositories\Oag\Crime\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AppealDetailController extends Controller
{
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
        $appeals = $this->appealDetailRepository->all();

        return view('oag.crime.appeal_details.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->criminalCaseRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    public function create($id = null)
    {
        if ($id) {
            $originalCase = $this->criminalCaseRepository->getById($id);
            
            if (!$originalCase || $originalCase->is_appeal_case || $originalCase->is_on_appeal) {
                return redirect()->route('crime.criminalCase.index')->with('error', 'Te case ae rineaki e aki tau ibukin appeal.');
            }

   
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




    public function store(Request $request, AppealDetailRepository $appealRepo)
{
    \Log::info('Incoming Appeal Detail form submission: ', $request->all());

    $validatedData = $request->validate([
        'case_id' => 'required|exists:cases,id',
        'appeal_case_number' => 'required|string',
        'filing_date_type' => 'required|in:court,defendant',
        'filing_date_value' => 'required|date',
        'court_outcome' => 'required|string',
        'judgment_delivered_date' => 'nullable|date',
        'verdict' => 'required|string',
        'decision_principle_established' => 'nullable|string',
    ]);

    // Map validated fields to actual columns
    // Map validated fields to actual columns
$data = [
    'case_id' => $validatedData['case_id'],
    'appeal_case_number' => $validatedData['appeal_case_number'],
    'court_outcome' => $validatedData['court_outcome'],
    'judgment_delivered_date' => $validatedData['judgment_delivered_date'] ?? null,
    'verdict' => $validatedData['verdict'],
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
        $data['appeal_filing_received_date'] = $validatedData['filing_date_value'];
    }

    try {
        $originalCase = $this->criminalCaseRepository->getById($validatedData['case_id']);

        // Optional: Update status or log
        // if ($originalCase) {
        //     $originalCase->status = 'appealed';
        //     $originalCase->save();
        // }

        $appeal = $appealRepo->create($data);
        \Log::info('Appeal stored successfully', $appeal->toArray());

        return redirect()->route('crime.criminalCase.index')->with('success', 'Appeal created successfully.');
    } catch (\Exception $e) {
        \Log::error('Error storing appeal: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Failed to store appeal.');
    }
}

    

    public function edit($id)
    {
        $appeal = $this->appealDetailRepository->getById($id);
        return view('oag.crime.appeal_details.edit', compact('appeal'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'appeal_case_number' => 'nullable|string|max:255',
            'appeal_filing_date' => 'nullable|date',
            'appeal_status' => 'required|in:pending,in_progress,decided,withdrawn',
            'appeal_grounds' => 'nullable|string',
            'appeal_decision' => 'nullable|string',
            'appeal_decision_date' => 'nullable|date',
        ]);

        $data['updated_by'] = Auth::id();

        try {
            $this->appealDetailRepository->update($id, $data);
            return redirect()->route('crime.appeals.index')->with('success', 'Appeal updated successfully.');
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
