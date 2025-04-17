<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\OAG\Crime\CourtsOfAppealRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class CourtOfAppealController extends Controller
{
    protected $courtOfAppealRepository;

    public function __construct(CourtOfAppealRepository $courtOfAppealRepository)
    {
        $this->courtOfAppealRepository = $courtOfAppealRepository;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->courtOfAppealRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    public function index()
    {
        return view('oag.courts.index');
    }

    public function create()
    {
        return view('oag.courts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'court_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();
        $data['updated_by'] = null;

        $this->courtOfAppealRepository->create($data);

        return redirect()->route('crime.courts.index')
            ->with('success', 'Court of Appeal created successfully.');
    }

    public function show($id)
    {
        $court = $this->courtOfAppealRepository->getById($id);
        if (!$court) {
            return response()->json(['message' => 'Court not found'], Response::HTTP_NOT_FOUND);
        }
        return view('oag.courts.show')->with('court', $court);
    }

    public function edit($id)
    {
        $court = $this->courtOfAppealRepository->getById($id);
        if (!$court) {
            return redirect()->route('crime.courts.index')->with('error', 'Court not found.');
        }
        return view('oag.courts.edit')->with('court', $court);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'court_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data['updated_by'] = auth()->id();

        $updated = $this->courtOfAppealRepository->update($id, $data);

        if (!$updated) {
            return redirect()->route('crime.courts.index')->with('error', 'Failed to update court.');
        }

        return redirect()->route('crime.courts.index')
            ->with('success', 'Court of Appeal updated successfully.');
    }

    public function destroy($id)
    {
        $deleted = $this->courtOfAppealRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Court not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Court of Appeal deleted successfully']);
    }
}
