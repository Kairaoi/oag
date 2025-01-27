<?php

namespace App\Http\Controllers\Oag\Draft;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Draft\BillCounselRepository; // Update the namespace for the BillCounselRepository
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class BillCounselController extends Controller
{
    protected $billCounselRepository;

    /**
     * BillCounselController constructor.
     *
     * @param BillCounselRepository $billCounselRepository
     */
    public function __construct(BillCounselRepository $billCounselRepository)
    {
        $this->billCounselRepository = $billCounselRepository;
    }

    /**
     * Display a listing of bill counsels.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.legal.bill_counsels.index'); // Update the view path if necessary
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->billCounselRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Show the form for creating a new bill counsel.
     *
     * @return Response
     */
    public function create()
    {
        return view('oag.legal.bill_counsels.create'); // Update the view path if necessary
    }

    /**
     * Store a newly created bill counsel in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'bill_id' => 'required|exists:bills,id', // Ensure bill exists
            'counsel_id' => 'required|exists:counsels,id', // Ensure counsel exists
            'assigned_date' => 'required|date',
            'due_date' => 'nullable|date',
            'role' => 'required|in:Lead,Support,Review',
        ]);

        // Prepare the data to be saved
        $data = [
            'bill_id' => $validated['bill_id'],
            'counsel_id' => $validated['counsel_id'],
            'assigned_date' => $validated['assigned_date'],
            'due_date' => $validated['due_date'] ?? null,
            'role' => $validated['role'],
        ];

        // Call the repository to create the record
        $this->billCounselRepository->create($data);

        // Redirect back with a success message
        return redirect()->route('legal.bill_counsels.index')->with('success', 'Bill Counsel assigned successfully.');
    }

    /**
     * Show the specified bill counsel.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $billCounsel = $this->billCounselRepository->getById($id);

        if (!$billCounsel) {
            return response()->json(['message' => 'Bill Counsel not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.legal.bill_counsels.show', compact('billCounsel')); // Update view path if necessary
    }

    /**
     * Show the form for editing the specified bill counsel.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $billCounsel = $this->billCounselRepository->getById($id);

        if (!$billCounsel) {
            return redirect()->route('legal.bill_counsels.index')->with('error', 'Bill Counsel not found.');
        }

        return view('oag.legal.bill_counsels.edit', compact('billCounsel')); // Update view path if necessary
    }

    /**
     * Update the specified bill counsel in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'counsel_id' => 'required|exists:counsels,id',
            'assigned_date' => 'required|date',
            'due_date' => 'nullable|date',
            'role' => 'required|in:Lead,Support,Review',
        ]);

        // Find the bill counsel by ID
        $billCounsel = $this->billCounselRepository->getById($id);

        // Ensure the bill counsel exists
        if (!$billCounsel) {
            return redirect()->route('legal.bill_counsels.index')->with('error', 'Bill Counsel not found.');
        }

        // Prepare the data to be updated
        $data = [
            'bill_id' => $validated['bill_id'],
            'counsel_id' => $validated['counsel_id'],
            'assigned_date' => $validated['assigned_date'],
            'due_date' => $validated['due_date'] ?? null,
            'role' => $validated['role'],
        ];

        // Update the bill counsel record
        $this->billCounselRepository->update($id, $data);

        // Redirect back with a success message
        return redirect()->route('legal.bill_counsels.index')->with('success', 'Bill Counsel updated successfully.');
    }

    /**
     * Remove the specified bill counsel from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->billCounselRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Bill Counsel not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Bill Counsel deleted successfully']);
    }
}
