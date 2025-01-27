<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Crime\OffenceRepository;
use App\Repositories\Oag\Crime\OffenceCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class OffenceController extends Controller
{
    protected $offenceRepository;

    /**
     * OffenceController constructor.
     *
     * @param OffenceRepository $offenceRepository
     */
    public function __construct(OffenceRepository $offenceRepository,
    OffenceCategoryRepository $offenceCategoryRepository)
    {
        $this->offenceRepository = $offenceRepository;
        $this->offenceCategoryRepository = $offenceCategoryRepository;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->offenceRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of the offences.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.offences.index');
    }

    /**
     * Show the form for creating a new offence.
     *
     * @return Response
     */
    public function create()
    {
    // Fetch categories using the pluck method
    $categories = $this->offenceCategoryRepository->pluck();

        return view('oag.offences.create')->with('categories', $categories);
    }

    /**
     * Store a newly created offence in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'offence_name' => 'required|string|max:255',
            'offence_category_id' => 'required|integer|exists:offence_categories,id',
        ]);

        $data['created_by'] = auth()->id(); 
        $data['updated_by'] = null;

        $offence = $this->offenceRepository->create($data);

        return redirect()->route('crime.offence.index')->with('success', 'Offence Created successfully.');
    }

    /**
     * Display the specified offence.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $offence = $this->offenceRepository->getById($id);

        if (!$offence) {
            return response()->json(['message' => 'Offence not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.offences.show')->with('offence', $offence);
    }

    /**
     * Show the form for editing the specified offence.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $offence = $this->offenceRepository->getById($id);
        $categories = $this->offenceCategoryRepository->pluck();

        if (!$offence) {
            return response()->json(['message' => 'Offence not found'], Response::HTTP_NOT_FOUND);
        }

        // Return view to edit the offence
        return view('oag.offences.edit')->with('categories', $categories)->with('offence', $offence);
    }

    /**
     * Update the specified offence in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'offence_name' => 'required|string|max:255',
            'offence_category_id' => 'required|integer|exists:offence_categories,id',
        ]);
        $data['updated_by'] = auth()->id(); 
        $updated = $this->offenceRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Offence not found or failed to update'], Response::HTTP_NOT_FOUND);
        }

        return redirect()->route('crime.offence.index')->with('success', 'Offence updated successfully.');
    }

    /**
     * Remove the specified offence from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->offenceRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Offence not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Offence deleted successfully']);
    }
}
