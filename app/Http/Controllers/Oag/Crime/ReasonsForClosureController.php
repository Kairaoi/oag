<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use App\Repositories\OAG\Crime\ReasonsForClosureRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class ReasonsForClosureController extends Controller
{
    protected $reasonsForClosureRepository;

    /**
     * ReasonsForClosureController constructor.
     *
     * @param ReasonsForClosureRepository $reasonsForClosureRepository
     */
    public function __construct(ReasonsForClosureRepository $reasonsForClosureRepository)
    {
        $this->reasonsForClosureRepository = $reasonsForClosureRepository;
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->reasonsForClosureRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Display a listing of the reasons for closure.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.reason.index');
    }

    /**
     * Show the form for creating a new reason for closure.
     *
     * @return Response
     */
    public function create()
    {
        return view('oag.reason.create');
    }

    /**
     * Store a newly created reason for closure in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'reason_description' => 'required|string',
        ]);

        $data['created_by'] = auth()->id(); 
        $data['updated_by'] = null;

        $reason = $this->reasonsForClosureRepository->create($data);

        return redirect()->route('crime.reason.index')->with('success', 'Reason for closure created successfully.');
    }

    /**
     * Display the specified reason for closure.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $reason = $this->reasonsForClosureRepository->getById($id);

        if (!$reason) {
            return response()->json(['message' => 'Reason for closure not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.reason.show')->with('reason', $reason);
    }

    /**
     * Show the form for editing the specified reason for closure.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $reason = $this->reasonsForClosureRepository->getById($id);

        return view('oag.reason.edit')->with('reason', $reason);
    }

    /**
     * Update the specified reason for closure in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'reason_description' => 'required|string',
        ]);

        $data['updated_by'] = auth()->id(); 

        $updated = $this->reasonsForClosureRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Reason for closure not found or failed to update'], Response::HTTP_NOT_FOUND);
        }

        return redirect()->route('crime.reason.index')->with('success', 'Reason for closure updated successfully.');
    }

    /**
     * Remove the specified reason for closure from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->reasonsForClosureRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Reason for closure not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Reason for closure deleted successfully']);
    }
}
