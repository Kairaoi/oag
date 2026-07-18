<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\CourtCaseRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class CourtCaseController extends Controller
{
    use AuthorizesCriminalCase;

    protected $courtCaseRepository;
    protected $criminalCaseRepository;
    protected $userRepository;

    public function __construct(
        CourtCaseRepository $courtCaseRepository,
        CriminalCaseRepository $criminalCaseRepository,
        UserRepository $userRepository
    ) {
        $this->courtCaseRepository = $courtCaseRepository;
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        return view('oag.court_cases.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->courtCaseRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    public function create($id)
    {
        abort_unless(auth()->user()->hasRole('cm.user'), 403);

        $case = $this->criminalCaseRepository->getById($id);
        abort_if(!$case, 404);
        $this->assertCaseIsDispatched($case);

        return view('oag.court_cases.create', compact('case'));
    }

    public function store(\App\Http\Requests\Oag\Crime\CourtCaseStoreRequest $request)
    {
        $case = $this->criminalCaseRepository->getById($request->input('case_id'));
        abort_if(!$case, 404);

        $data = $request->validated();

        $data['created_by'] = auth()->id();
        $data['updated_by'] = null;

        $this->courtCaseRepository->create($data);

        return redirect()->route('crime.criminalCase.index')
            ->with('success', 'Court case created successfully.');
    }

    public function show($id)
{
    // Fetch the court case by its ID
    $courtCase = $this->courtCaseRepository->getById($id);

    // Check if the court case exists
    if (!$courtCase) {
        return redirect()->route('crime.court-cases.index')
            ->with('error', 'Court case not found.');
    }

    // Return the 'show' view with the court case data
    return view('oag.court_cases.show', compact('courtCase'));
}


    public function edit($id)
    {
        $courtCase = $this->courtCaseRepository->getById($id);

        if (!$courtCase) {
            return redirect()->route('crime.court-cases.index')
                ->with('error', 'Court case not found.');
        }

        $cases = $this->criminalCaseRepository->pluck();

        return view('oag.court_cases.edit', compact('courtCase', 'cases'));
    }

    public function update(\App\Http\Requests\Oag\Crime\CourtCaseUpdateRequest $request, $id)
    {
        $courtCase = $this->courtCaseRepository->getById($id);

        $data = $request->validated();

        $data['updated_by'] = auth()->id();

        $this->courtCaseRepository->update($id, $data);

        return redirect()->route('crime.court-cases.index')
            ->with('success', 'Court case updated successfully.');
    }

    public function destroy($id)
    {
        $deleted = $this->courtCaseRepository->deleteById($id);

        if (!$deleted) {
            return redirect()->route('crime.court-cases.index')
                ->with('error', 'Court case not found or could not be deleted.');
        }

        return redirect()->route('crime.court-cases.index')
            ->with('success', 'Court case deleted successfully.');
    }
}
