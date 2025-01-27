<?php

namespace App\Http\Controllers\Oag\Draft;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Draft\MinistryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class MinistryController extends Controller
{
    protected $ministryRepository;

    public function __construct(MinistryRepository $ministryRepository)
    {
        $this->ministryRepository = $ministryRepository;
    }

    public function index()
    {
        return view('oag.draft.ministries.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->ministryRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    public function create()
    {
        return view('oag.draft.ministries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:ministries|max:255',
        ]);

        $data = $validated;
        $this->ministryRepository->create($data);

        return redirect()->route('draft.ministry.index')->with('success', 'Ministry created successfully.');
    }

    public function edit($id)
    {
        $ministry = $this->ministryRepository->getById($id);

        if (!$ministry) {
            return redirect()->route('draft.ministry.index')->with('error', 'Ministry not found.');
        }

        return view('oag.draft.ministries.edit', compact('ministry'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:ministries,code,' . $id,
        ]);

        $data = $validated;
        $this->ministryRepository->update($id, $data);

        return redirect()->route('draft.ministry.index')->with('success', 'Ministry updated successfully.');
    }

    public function destroy($id)
    {
        $deleted = $this->ministryRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Ministry not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Ministry deleted successfully']);
    }

    public function show($id)
{
    // Get the ministry by ID
    $ministry = $this->ministryRepository->getById($id);
    
    // Check if ministry exists
    if (!$ministry) {
        return response()->json(['message' => 'Ministry not found'], Response::HTTP_NOT_FOUND);
    }

    // Return the view with the ministry data
    return view('oag.draft.ministries.show')->with('ministry', $ministry);
}

}
