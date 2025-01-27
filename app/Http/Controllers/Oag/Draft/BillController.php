<?php

namespace App\Http\Controllers\Oag\Draft;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Draft\BillRepository;
use App\Repositories\Oag\Draft\MinistryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DataTables;

class BillController extends Controller
{
    protected $billRepository;
    protected $ministryRepository;

    public function __construct(BillRepository $billRepository, MinistryRepository $ministryRepository  )
    {
        $this->billRepository = $billRepository;
        $this->ministryRepository = $ministryRepository;
    }

    public function index()
    {
        return view('oag.draft.bill.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');

        $query = $this->billRepository->getForDataTable($search);

        return DataTables::of($query)->make(true);
    }

    public function create()
    {
        $ministries = $this->ministryRepository->pluck();
        return view('oag.draft.bill.create')->with('ministries', $ministries);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:bills,name',
            'receipt_date' => 'required|date',
            'ministry_id' => 'required|exists:ministries,id',
            'status' => 'required|in:Draft,First Reading,Second Reading,Third Reading,Passed,Rejected',
            'priority' => 'required|in:Normal,Urgent,High Priority',
            'task' => 'required|string|max:255',
            'progress_status' => 'required|in:Not Started,Ongoing,Achieved',
            'comments' => 'nullable|string',
            'target_completion_date' => 'nullable|date|after_or_equal:receipt_date',
            'actual_completion_date' => 'nullable|date|after_or_equal:receipt_date',
            'version' => 'required|string|max:10', // Adjust length if needed
        ]);
        

        $data = $validated;
        $this->billRepository->create($data);

        return redirect()->route('draft.bills.index')->with('success', 'Bill created successfully.');
    }

    public function edit($id)
    {
        $bill = $this->billRepository->getById($id);
        $ministries = $this->ministryRepository->pluck();

        if (!$bill) {
            return redirect()->route('bills.index')->with('error', 'Bill not found.');
        }

        return view('oag.draft.bill.edit', compact('bill'))->with('ministries', $ministries);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'receipt_date' => 'required|date',
            'ministry_id' => 'required|exists:ministries,id',
            'status' => 'required|in:Draft,First Reading,Second Reading,Third Reading,Passed,Rejected',
            'priority' => 'required|in:Normal,Urgent,High Priority',
            'task' => 'required|string|max:255',
            'progress_status' => 'required|in:Not Started,Ongoing,Achieved',
            'comments' => 'nullable|string',
            'target_completion_date' => 'nullable|date|after_or_equal:receipt_date',
            'actual_completion_date' => 'nullable|date|after_or_equal:receipt_date',
            'version' => 'required|string|max:10', // Adjust length if needed
        ]);

        $data = $validated;
        $this->billRepository->update($id, $data);

        return redirect()->route('draft.bills.index')->with('success', 'Bill updated successfully.');
    }

    public function destroy($id)
    {
        $deleted = $this->billRepository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Bill not found or failed to delete'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Bill deleted successfully']);
    }
}
