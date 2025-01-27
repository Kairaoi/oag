<?php

namespace App\Http\Controllers\Oag\Civil;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Civil\CourtCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class CourtCategoryController extends Controller
{
    protected $courtCategoryRepository;

    /**
     * CourtCategoryController constructor.
     *
     * @param CourtCategoryRepository $courtCategoryRepository
     */
    public function __construct(CourtCategoryRepository $courtCategoryRepository)
    {
        $this->courtCategoryRepository = $courtCategoryRepository;
    }

    /**
     * Display a listing of court categories.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.civil.court_categories.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->courtCategoryRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Show the form for creating a new court category.
     *
     * @return Response
     */
    public function create()
    {
        return view('oag.civil.court_categories.create');
    }

    /**
     * Store a newly created court category in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10', // Added 'code' validation to match migration schema
        ]);

        $data['created_by'] = auth()->id();
        $data['updated_by'] = null;

        $this->courtCategoryRepository->create($data);

        return redirect()->route('civil.courtcategory.index')->with('success', 'Court category created successfully.');
    }

    /**
     * Display the specified court category.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $courtCategory = $this->courtCategoryRepository->getById($id);

        if (!$courtCategory) {
            return response()->json(['message' => 'Court category not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.civil.court_categories.show', compact('courtCategory'));
    }

    /**
     * Show the form for editing the specified court category.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $courtCategory = $this->courtCategoryRepository->getById($id);

        if (!$courtCategory) {
            return redirect()->route('civil.court_categories.index')->with('error', 'Court category not found.');
        }

        return view('oag.civil.court_categories.edit', compact('courtCategory'));
    }

    /**
     * Update the specified court category in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10', // Added 'code' validation
        ]);

        $data['updated_by'] = auth()->id();

        $updated = $this->courtCategoryRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Court category not found or failed to update'], Response::HTTP_NOT_FOUND);
        }

        return redirect()->route('civil.courtcategory.index')->with('success', 'Court category updated successfully.');
    }

    /**
     * Remove the specified court category from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->courtCategoryRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Court category not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Court category deleted successfully']);
    }
}
