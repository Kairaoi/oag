<?php

namespace App\Http\Controllers\Oag\Draft;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Draft\RegulationCounselRepository; // Update the namespace for the RegulationCounselRepository
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class RegulationCounselController extends Controller
{
    protected $regulationCounselRepository;

    /**
     * RegulationCounselController constructor.
     *
     * @param RegulationCounselRepository $regulationCounselRepository
     */
    public function __construct(RegulationCounselRepository $regulationCounselRepository)
    {
        $this->regulationCounselRepository = $regulationCounselRepository;
    }

    /**
     * Display a listing of regulation counsels.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.legal.regulation_counsels.index'); // Update the view path if necessary
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->regulationCounselRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Show the form for creating a new regulation counsel.
     *
     * @return Response
     */
    public function create()
    {
        return view('oag.legal.regulation_counsels.create'); // Update the view path if necessary
    }

    /**
     * Store a newly created regulation counsel in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'regulation_id' => 'required|exists:regulations,id', // Ensure regulation exists
            'counsel_id' => 'required|exists:counsels,id', // Ensure counsel exists
            'assigned_date' => 'required|date',
            'due_date' => 'nullable|date',
            'role' => 'required|in:Lead,Support,Review',
        ]);

        // Prepare the data to be saved
        $data = [
            'regulation_id' => $validated['regulation_id'],
            'counsel_id' => $validated['counsel_id'],
            'assigned_date' => $validated['assigned_date'],
            'due_date' => $validated['due_date'] ?? null,
            'role' => $validated['role'],
        ];

        // Call the repository to create the record
        $this->regulationCounselRepository->create($data);

        // Redirect back with a success message
        return redirect()->route('legal.regulation_counsels.index')->with('success', 'Regulation Counsel assigned successfully.');
    }

    /**
     * Show the specified regulation counsel.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $regulationCounsel = $this->regulationCounselRepository->getById($id);

        if (!$regulationCounsel) {
            return response()->json(['message' => 'Regulation Counsel not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.legal.regulation_counsels.show', compact('regulationCounsel')); // Update view path if necessary
    }

    /**
     * Show the form for editing the specified regulation counsel.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $regulationCounsel = $this->regulationCounselRepository->getById($id);

        if (!$regulationCounsel) {
            return redirect()->route('legal.regulation_counsels.index')->with('error', 'Regulation Counsel not found.');
        }

        return view('oag.legal.regulation_counsels.edit', compact('regulationCounsel')); // Update view path if necessary
    }

    /**
     * Update the specified regulation counsel in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'regulation_id' => 'required|exists:regulations,id',
            'counsel_id' => 'required|exists:counsels,id',
            'assigned_date' => 'required|date',
            'due_date' => 'nullable|date',
            'role' => 'required|in:Lead,Support,Review',
        ]);

        // Find the regulation counsel by ID
        $regulationCounsel = $this->regulationCounselRepository->getById($id);

        // Ensure the regulation counsel exists
        if (!$regulationCounsel) {
            return redirect()->route('legal.regulation_counsels.index')->with('error', 'Regulation Counsel not found.');
        }

        // Prepare the data to be updated
        $data = [
            'regulation_id' => $validated['regulation_id'],
            'counsel_id' => $validated['counsel_id'],
            'assigned_date' => $validated['assigned_date'],
            'due_date' => $validated['due_date'] ?? null,
            'role' => $validated['role'],
        ];

        // Update the regulation counsel record
        $this->regulationCounselRepository->update($id, $data);

        // Redirect back with a success message
        return redirect()->route('legal.regulation_counsels.index')->with('success', 'Regulation Counsel updated successfully.');
    }

    /**
     * Remove the specified regulation counsel from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->regulationCounselRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Regulation Counsel not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Regulation Counsel deleted successfully']);
    }
}
