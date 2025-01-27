<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\OAG\Crime\CouncilRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class CouncilController extends Controller
{
    protected $councilRepository;

    /**
     * CouncilController constructor.
     *
     * @param CouncilRepository $councilRepository
     */
    public function __construct(CouncilRepository $councilRepository)
    {
        $this->councilRepository = $councilRepository;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->councilRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of the council.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.council.index');
    }

    /**
     * Show the form for creating a new council.
     *
     * @return Response
     */
    public function create()
    {
        return view('oag.council.create');
    }

    /**
     * Store a newly created council in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
{
    $data = $request->validate([
        'council_name' => 'required|string|max:255',
    ]);

    // Set the current user as the creator and default updated_by to null
    $data['created_by'] = auth()->id();
    $data['updated_by'] = null; // or set a default value if necessary

    // Store the new council
    $council = $this->councilRepository->create($data);

   // Redirect to the index route with a success message
   return redirect()->route('crime.council.index')->with('success', 'Council created successfully.');
}


    /**
     * Display the specified council.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $council = $this->councilRepository->getById($id);

        if (!$council) {
            return response()->json(['message' => 'Council not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.council.show')->with('council', $council);
    }

    /**
     * Show the form for editing the specified council.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $council = $this->councilRepository->getById($id);

       

        return view('oag.council.edit')->with('council', $council);
    }

    /**
     * Update the specified council in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
{
    $data = $request->validate([
        'council_name' => 'required|string|max:255',
        'updated_by' => 'nullable|exists:users,id',
    ]);

    // Set the current user as the updater
    $data['updated_by'] = auth()->id();

    // Perform the update operation
    $updated = $this->councilRepository->update($id, $data);

    // Check if the update was successful
    if (!$updated) {
        // Redirect back with an error message if update failed
        return redirect()->route('crime.council.index')->with('error', 'Council not found or failed to update.');
    }

    // Redirect back to the index with a success message
    return redirect()->route('crime.council.index')->with('success', 'Council updated successfully.');
}

    /**
     * Remove the specified council from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        // Attempt to delete the council by its ID
        $deleted = $this->councilRepository->deleteById($id);
    
        // Check if the delete operation was successful
        if (!$deleted) {
            // Redirect back to the index route with an error message if deletion failed
            return redirect()->route('crime.council.index')->with('error', 'Council not found or failed to delete.');
        }
    
        // Redirect back to the index route with a success message if deletion succeeded
        return redirect()->route('crime.council.index')->with('success', 'Council deleted successfully.');
    }
    
}
