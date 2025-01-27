<?php

namespace App\Http\Controllers\Oag\Draft;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Draft\CounselRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class CounselController extends Controller
{
    protected $counselRepository;

    public function __construct(CounselRepository $counselRepository)
    {
        $this->counselRepository = $counselRepository;
    }

    public function index()
    {
        return view('oag.draft.counsel.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->counselRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    public function create()
    {
        return view('oag.draft.counsel.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|in:DLD,Senior Counsel,Junior Counsel,AG',
        ]);

        $data = $validated;
        $this->counselRepository->create($data);

        return redirect()->route('draft.counsels.index')->with('success', 'Counsel created successfully.');
    }

    public function edit($id)
    {
        $counsel = $this->counselRepository->getById($id);

        if (!$counsel) {
            return redirect()->route('draft.counsels.index')->with('error', 'Counsel not found.');
        }

        return view('oag.draft.counsel.edit', compact('counsel'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|in:DLD,Senior Counsel,Junior Counsel,AG',
        ]);

        $data = $validated;
        $this->counselRepository->update($id, $data);

        return redirect()->route('draft.counsels.index')->with('success', 'Counsel updated successfully.');
    }

    public function destroy($id)
    {
        $deleted = $this->counselRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Counsel not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Counsel deleted successfully']);
    }
}
