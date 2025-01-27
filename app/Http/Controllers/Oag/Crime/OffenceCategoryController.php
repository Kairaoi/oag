<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Crime\OffenceCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class OffenceCategoryController extends Controller
{
    protected $offenceCategoryRepository;

    /**
     * OffenceCategoryController constructor.
     *
     * @param OffenceCategoryRepository $offenceCategoryRepository
     */
    public function __construct(OffenceCategoryRepository $offenceCategoryRepository)
    {
        $this->offenceCategoryRepository = $offenceCategoryRepository;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->offenceCategoryRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of the offence categories.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.category.index');
    }

    /**
     * Show the form for creating a new offence category.
     *
     * @return Response
     */
    public function create()
    {
        // Return view to create a new offence category
        return view('oag.category.create');
    }

    /**
     * Store a newly created offence category in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
{
    $data = $request->validate([
        'category_name' => 'required|string|max:255',
    ]);

    \Log::info('Storing Offence Category', $data);

    $data['created_by'] = auth()->id();
    $data['updated_by'] = null;

    $offenceCategory = $this->offenceCategoryRepository->create($data);

    \Log::info('Offence Category Created', ['id' => $offenceCategory->id]);

    return response()->json([
        'success' => true,
        'id' => $offenceCategory->id,
    ]);
}


    /**
     * Display the specified offence category.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $offenceCategory = $this->offenceCategoryRepository->getById($id);

        if (!$offenceCategory) {
            return response()->json(['message' => 'Offence Category not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.category.show')->with('offenceCategory', $offenceCategory);
    }

    /**
     * Show the form for editing the specified offence category.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $offenceCategory = $this->offenceCategoryRepository->getById($id);

        if (!$offenceCategory) {
            return response()->json(['message' => 'Offence Category not found'], Response::HTTP_NOT_FOUND);
        }

        // Return view to edit the offence category
        return view('oag.category.edit', compact('offenceCategory'));
    }

    /**
     * Update the specified offence category in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        $data['updated_by'] = auth()->id();  

        $updated = $this->offenceCategoryRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Offence Category not found or failed to update'], Response::HTTP_NOT_FOUND);
        }

       // Redirect to the index route with a success message
   return redirect()->route('crime.category.index')->with('success', 'Offence Category Updated successfully.');
    }

    /**
     * Remove the specified offence category from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->offenceCategoryRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Offence Category not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return redirect()->route('crime.category.index')->with('success', 'Offence Category Deleted successfully.');
    }
}
