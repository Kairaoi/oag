<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Crime\CaseReallocationRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class CaseReallocationController extends Controller
{
    protected $caseReallocationRepository;
    protected $criminalCaseRepository;
    protected $userRepository;

    public function __construct(
        CaseReallocationRepository $caseReallocationRepository,
        CriminalCaseRepository $criminalCaseRepository,
        UserRepository $userRepository
    ) {
        $this->caseReallocationRepository = $caseReallocationRepository;
        $this->criminalCaseRepository = $criminalCaseRepository;
        $this->userRepository = $userRepository;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->caseReallocationRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    public function index()
    {
        return view('oag.crime.reallocations.index');
    }

    public function create()
    {
        $cases = $this->criminalCaseRepository->pluck();
        $lawyers = $this->userRepository->pluck();

        return view('oag.crime.reallocations.create')
            ->with('cases', $cases)
            ->with('lawyers', $lawyers);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'case_id'              => 'required|exists:cases,id',
            'from_lawyer_id'       => 'required|exists:users,id',
            'to_lawyer_id'         => 'required|exists:users,id|different:from_lawyer_id',
            'reallocation_reason'  => 'required|string',
            'reallocation_date'    => 'required|date',
        ]);

        $data['created_by'] = auth()->id();
        $caseReallocation = $this->caseReallocationRepository->create($data);

        return redirect()->route('crime.caseReallocation.index')
                         ->with('success', 'Case reallocated successfully.');
    }

    public function show($id)
    {
        $caseReallocation = $this->caseReallocationRepository->getById($id);

        if (!$caseReallocation) {
            return response()->json(['message' => 'Case reallocation not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.crime.reallocations.show', compact('caseReallocation'));
    }

    public function edit($id)
    {
        $caseReallocation = $this->caseReallocationRepository->getById($id);
        
        if (!$caseReallocation) {
            return response()->json(['message' => 'Case reallocation not found'], Response::HTTP_NOT_FOUND);
        }
        
        $cases = $this->criminalCaseRepository->pluck();
        $lawyers = $this->userRepository->pluck();
        
        return view('oag.crime.reallocations.edit')
            ->with('caseReallocation', $caseReallocation)
            ->with('cases', $cases)
            ->with('lawyers', $lawyers);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'case_id'              => 'required|exists:cases,id',
            'from_lawyer_id'       => 'required|exists:users,id',
            'to_lawyer_id'         => 'required|exists:users,id|different:from_lawyer_id',
            'reallocation_reason'  => 'required|string',
            'reallocation_date'    => 'required|date',
        ]);

        $updated = $this->caseReallocationRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Case reallocation not found or failed to update'], Response::HTTP_NOT_FOUND);
        }

        return redirect()->route('crime.caseReallocation.index')
                         ->with('success', 'Case reallocation updated successfully.');
    }

    public function destroy($id)
    {
        $deleted = $this->caseReallocationRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Case reallocation not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return redirect()->route('crime.caseReallocation.index')
                         ->with('success', 'Case reallocation deleted successfully.');
    }
}
