<?php

namespace App\Http\Controllers\Oag\Legal;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Legal\LegalTaskRepository; // Update namespace for the repository
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class LegalTaskController extends Controller
{
    protected $legalTaskRepository;

    /**
     * LegalTaskController constructor.
     *
     * @param LegalTaskRepository $legalTaskRepository
     */
    public function __construct(LegalTaskRepository $legalTaskRepository)
    {
        $this->legalTaskRepository = $legalTaskRepository;
    }

    /**
     * Display a listing of legal tasks.
     *
     * @return Response
     */
    public function index()
    {
        return view('oag.legal.tasks.index'); // Update view path if necessary
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->legalTaskRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    /**
     * Show the form for creating a new legal task.
     *
     * @return Response
     */
    public function create()
    {
        return view('oag.legal.tasks.create'); // Update view path if necessary
    }

    /**
     * Store a newly created legal task in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
{
    // Validate the incoming request data
    $validated = $request->validate([
        'date' => 'required|date',
        'task' => 'required|string',
        'ministry' => 'required|string',
        'legal_advice_meeting' => 'required|string',
        'allocated_date' => 'nullable|date',
        'allocated_to' => 'nullable|exists:users,id', // Validate user ID exists in users table
        'status' => 'nullable|string', // 'status' is optional
        'onward_action' => 'nullable|string',
        'date_task_achieved' => 'nullable|date',
        'date_approved_by_ag' => 'nullable|date',
        'meeting_date' => 'nullable|date',
        'time_frame' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    // Prepare the data to be saved
    $data = [
        'date' => $validated['date'], // Add the 'date' field
        'task' => $validated['task'],
        'ministry' => $validated['ministry'],
        'legal_advice_meeting' => $validated['legal_advice_meeting'],
        'allocated_date' => $validated['allocated_date'] ?? null,
        'allocated_to' => $validated['allocated_to'] ?? null,
        'status' => $validated['status'] ?? null, // Ensure 'status' has a default value
        'onward_action' => $validated['onward_action'] ?? null,
        'date_task_achieved' => $validated['date_task_achieved'] ?? null,
        'date_approved_by_ag' => $validated['date_approved_by_ag'] ?? null,
        'meeting_date' => $validated['meeting_date'] ?? null,
        'time_frame' => $validated['time_frame'] ?? null,
        'notes' => $validated['notes'] ?? null,
        'created_by' => auth()->id(),
        'updated_by' => null,
    ];

    // Call the repository to create the record
    $this->legalTaskRepository->create($data);

    // Redirect back with a success message
    return redirect()->route('legal.legal_tasks.index')->with('success', 'Legal task created successfully.');
}

    /**
     * Display the specified legal task.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $legalTask = $this->legalTaskRepository->getById($id);

        if (!$legalTask) {
            return response()->json(['message' => 'Legal task not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.civil.legal_tasks.show', compact('legalTask')); // Update view path if necessary
    }

    /**
     * Show the form for editing the specified legal task.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $legalTask = $this->legalTaskRepository->getById($id);

        if (!$legalTask) {
            return redirect()->route('legal.legal_task.index')->with('error', 'Legal task not found.');
        }

        return view('oag.legal.tasks.edit', compact('legalTask')); // Update view path if necessary
    }

    /**
     * Update the specified legal task in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'date' => 'required|date',
            'task' => 'required|string',
            'ministry' => 'required|string',
            'legal_advice_meeting' => 'required|string',
            'allocated_date' => 'nullable|date',
            'allocated_to' => 'nullable|exists:users,id', // Validate user ID exists in users table
            'status' => 'nullable|string', // 'status' is optional
            'onward_action' => 'nullable|string',
            'date_task_achieved' => 'nullable|date',
            'date_approved_by_ag' => 'nullable|date',
            'meeting_date' => 'nullable|date',
            'time_frame' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
    
        // Find the legal task by ID
        $legalTask = $this->legalTaskRepository->getById($id);
    
        // Ensure the legal task exists
        if (!$legalTask) {
            return redirect()->route('civil.legal_task.index')->with('error', 'Legal task not found.');
        }
    
        // Prepare the data to be updated
        $data = [
            'date' => $validated['date'], // Update the 'date' field
            'task' => $validated['task'],
            'ministry' => $validated['ministry'],
            'legal_advice_meeting' => $validated['legal_advice_meeting'],
            'allocated_date' => $validated['allocated_date'] ?? null,
            'allocated_to' => $validated['allocated_to'] ?? null,
            'status' => $validated['status'] ?? null, // Ensure 'status' is handled
            'onward_action' => $validated['onward_action'] ?? null,
            'date_task_achieved' => $validated['date_task_achieved'] ?? null,
            'date_approved_by_ag' => $validated['date_approved_by_ag'] ?? null,
            'meeting_date' => $validated['meeting_date'] ?? null,
            'time_frame' => $validated['time_frame'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'updated_by' => auth()->id(), // Set the 'updated_by' field
        ];
    
        // Update the legal task record
        $this->legalTaskRepository->update($id, $data);
    
        // Redirect back with a success message
        return redirect()->route('legal.legal_tasks.index')->with('success', 'Legal task updated successfully.');
    }
    

    /**
     * Remove the specified legal task from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $deleted = $this->legalTaskRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Legal task not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Legal task deleted successfully']);
    }
}
