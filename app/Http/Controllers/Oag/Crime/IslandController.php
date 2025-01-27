<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Crime\IslandRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class IslandController extends Controller
{
    protected $islandRepository;

    /**
     * IslandController constructor.
     *
     * @param IslandRepository $islandRepository
     */
    public function __construct(IslandRepository $islandRepository)
    {
        $this->islandRepository = $islandRepository;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->islandRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of the island.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.island.index');
    }

    /**
     * Show the form for creating a new island.
     *
     * @return Response
     */
    public function create()
    {
        return view('oag.island.create');
    }

    /**
     * Store a newly created island in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'island_name' => 'required|string|max:255',
           
        ]);
    
        $data['created_by'] = auth()->id();  // Set the current user as the creator
        $data['updated_by'] = null;  // Set the current user as the updater
    
        $island = $this->islandRepository->create($data);
    
        return redirect()->route('crime.island.index')->with('success', 'Island created successfully.');
    }
    

    /**
     * Display the specified island.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $island = $this->islandRepository->getById($id);

        if (!$island) {
            return response()->json(['message' => 'Island not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.island.show')->with('island', $island);
    }

    /**
     * Show the form for editing the specified island.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $island = $this->islandRepository->getById($id);

        if (!$island) {
            return response()->json(['message' => 'Island not found'], Response::HTTP_NOT_FOUND);
        }

        // Return view to edit the island
        return view('oag.island.edit')->with('island', $island);
    }

    /**
     * Update the specified island in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'island_name' => 'required|string|max:255',
        ]);
    
        $data['updated_by'] = auth()->id();  // Set the current user as the updater
    
        $updated = $this->islandRepository->update($id, $data);
    
        if (!$updated) {
            return redirect()->route('crime.island.index')->with('error', 'Island not found or failed to update.');
        }
    
        return redirect()->route('crime.island.index')->with('success', 'Island updated successfully.');
    }
    

    /**
     * Remove the specified island from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
{
    $deleted = $this->islandRepository->deleteById($id);

    if (!$deleted) {
        return redirect()->route('crime.island.index')->with('error', 'Island not found or failed to delete.');
    }

    return redirect()->route('crime.island.index')->with('success', 'Island deleted successfully.');
}

}
