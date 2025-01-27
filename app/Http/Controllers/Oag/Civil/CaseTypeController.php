<?php

namespace App\Http\Controllers\Oag\Civil;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Civil\CaseTypeRepository; // Update namespace for the repository
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class CaseTypeController extends Controller
{
    protected $caseTypeRepository;

    /**
     * CaseTypeController constructor.
     *
     * @param CaseTypeRepository $caseTypeRepository
     */
    public function __construct(CaseTypeRepository $caseTypeRepository)
    {
        $this->caseTypeRepository = $caseTypeRepository;
    }

    /**
     * Display a listing of case types.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.civil.case_types.index'); // Update view path if necessary
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->caseTypeRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Show the form for creating a new case type.
     *
     * @return Response
     */
    public function create()
    {
        return view('oag.civil.case_types.create'); // Update view path if necessary
    }

    /**
     * Store a newly created case type in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data['created_by'] = auth()->id();
        $data['updated_by'] = null;

        $this->caseTypeRepository->create($data);

        return redirect()->route('civil.casetype.index')->with('success', 'Case type created successfully.');
    }

    /**
     * Display the specified case type.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $caseType = $this->caseTypeRepository->getById($id);

        if (!$caseType) {
            return response()->json(['message' => 'Case type not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.civil.case_types.show', compact('caseType')); // Update view path if necessary
    }

    /**
     * Show the form for editing the specified case type.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $caseType = $this->caseTypeRepository->getById($id);

        if (!$caseType) {
            return redirect()->route('civil.case_types.index')->with('error', 'Case type not found.');
        }

        return view('oag.civil.case_types.edit', compact('caseType')); // Update view path if necessary
    }

    /**
     * Update the specified case type in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data['updated_by'] = auth()->id();

        $updated = $this->caseTypeRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Case type not found or failed to update'], Response::HTTP_NOT_FOUND);
        }

        return redirect()->route('civil.casetype.index')->with('success', 'Case type updated successfully.');
    }

    /**
     * Remove the specified case type from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->caseTypeRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Case type not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Case type deleted successfully']);
    }
}
