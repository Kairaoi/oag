<?php

namespace App\Http\Controllers\Oag\Civil2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Oag\Civil2\CaseClosureRepository;
use App\Models\Oag\Civil2\Civil2Case;

class CaseClosureController extends Controller
{
    protected $closureRepo;

    public function __construct(CaseClosureRepository $closureRepo)
    {
        $this->closureRepo = $closureRepo;
    }

    /**
     * Show the closure form for a case.
     */
    public function create($caseId)
    {
        $case = $this->closureRepo->getById($caseId);

        if (!$case) {
            return redirect()->back()->withErrors('Case not found.');
        }

        return view('oag.civil2.closure.create', compact('case'));
    }

    /**
     * Store closure record and mark case as closed.
     */
    public function store(Request $request, $caseId)
    {
        $validated = $request->validate([
            'memo_date' => 'required|date',
            'sg_clearance' => 'boolean',
            'sg_clearance_date' => 'nullable|date',
            'ag_endorsement' => 'boolean',
            'ag_endorsement_date' => 'nullable|date',
            'file_archived' => 'boolean',
            'file_archived_date' => 'nullable|date',
            'closure_notes' => 'nullable|string',
        ]);

        $validated['case_id'] = $caseId;
        $validated['closed_by'] = auth()->id();
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = null;

        // Create closure record
        $this->closureRepo->create($validated);

        // Update case status using closureRepo to get case
        $case = $this->closureRepo->getById($caseId);

        if ($case instanceof Civil2Case) {
            $case->case_status_id = config('civil2.status.closed'); // e.g. 'Closed'
            $case->date_closed = now();
            $case->updated_by = auth()->id();
            $case->save();
        }

        return redirect()->route('civil2.cases.index')->with('success', 'Case closed successfully.');
    }

    /**
     * Show a specific case closure.
     */
    public function show($id)
    {
        $closure = $this->closureRepo->getById($id);

        if (!$closure) {
            return response()->json(['message' => 'Case closure not found'], Response::HTTP_NOT_FOUND);
        }

        return view('oag.civil2.closure.show', compact('closure'));
    }

    /**
     * Reopen a case and log reversal.
     */
    public function reopen($caseId)
    {
        $case = $this->closureRepo->getById($caseId);

        if (!$case) {
            return redirect()->back()->withErrors('Case not found.');
        }

        if ($case instanceof Civil2Case) {
            $case->date_closed = null;
            $case->case_status_id = config('civil2.status.active'); // e.g. 'Reopened'
            $case->updated_by = auth()->id();
            $case->save();
        }

        return redirect()->back()->with('success', 'Case reopened successfully.');
    }

    /**
 * Close a case by marking it as closed.
 */
public function close($caseId)
{
    // Retrieve the case using the repository
    $case = $this->closureRepo->getById($caseId);
    dd($case);
    // Check if the case exists
    if (!$case) {
        return redirect()->back()->withErrors('Case not found.');
    }

    // Check if the case is already closed
    if ($case->case_status_id === config('civil2.status.closed')) {
        return redirect()->back()->with('info', 'Case is already closed.');
    }

    // Update the case status to closed
    $case->case_status_id = config('civil2.status.closed');
    $case->date_closed = now();
    $case->updated_by = auth()->id();
    $case->save();

    return redirect()->back()->with('success', 'Case closed successfully.');
}

}
