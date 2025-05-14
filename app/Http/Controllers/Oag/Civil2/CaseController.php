<?php

namespace App\Http\Controllers\Oag\Civil2;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Civil2\CaseRepository;
use App\Repositories\Oag\Civil\CourtCategoryRepository;
use App\Repositories\Oag\Civil2\CauseOfActionRepository;
use App\Repositories\Oag\Civil2\CaseStatusRepository;
use App\Repositories\Oag\Civil2\CasePendingStatusRepository;
use App\Repositories\Oag\Civil2\CaseOriginTypeRepository;
use App\Repositories\Oag\Crime\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Models\Oag\Civil2\Civil2Case;
use DataTables;

class CaseController extends Controller
{
    protected $caseRepo, $courtRepo, $causeRepo, $statusRepo, $pendingStatusRepo, $originRepo, $userRepo;

    public function __construct(
        CaseRepository $caseRepo,
        CourtCategoryRepository $courtRepo,
        CauseOfActionRepository $causeRepo,
        CaseStatusRepository $statusRepo,
        CasePendingStatusRepository $pendingStatusRepo,
        CaseOriginTypeRepository $originRepo,
        UserRepository $userRepo
    ) {
        $this->caseRepo = $caseRepo;
        $this->courtRepo = $courtRepo;
        $this->causeRepo = $causeRepo;
        $this->statusRepo = $statusRepo;
        $this->pendingStatusRepo = $pendingStatusRepo;
        $this->originRepo = $originRepo;
        $this->userRepo = $userRepo;
    }

    public function index()
    {
        return view('oag.civil2.civil_cases.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->caseRepo->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    public function create()
    {
        return view('oag.civil2.civil_cases.create', [
            'courtCategories' => $this->courtRepo->pluck(),
            'causesOfAction' => $this->causeRepo->pluck(),
            'caseStatuses' => $this->statusRepo->pluck(),
            'casePendingStatuses' => $this->pendingStatusRepo->pluck(),
            'caseOriginTypes' => $this->originRepo->pluck(),
            'counsels' => $this->userRepo->pluck()
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Civil2Case store request started', ['data' => $request->all()]);
        
        $data = $request->validate([
            'case_file_no' => 'required|string|unique:civil2_cases,case_file_no',
            'court_case_no' => 'nullable|string|max:255',
            'case_name' => 'required|string|max:255',
            'date_received' => 'nullable|date',
            'date_opened' => 'required|date',
            'date_closed' => 'nullable|date|after_or_equal:date_opened',
            'court_type_id' => 'required|exists:court_categories,id',
            'cause_of_action_id' => 'required|exists:causes_of_action,id',
            'responsible_counsel_id' => 'required|exists:users,id',
            'case_status_id' => 'required|exists:case_statuses,id',
            'case_pending_status_id' => 'nullable|exists:case_pending_statuses,id',
            'case_origin_type_id' => 'required|exists:case_origin_types,id',
            'case_description' => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();

        DB::beginTransaction();
        try {
            $case = $this->caseRepo->create($data);
            DB::commit();

            Log::info('Civil2Case created successfully', ['id' => $case->id]);
            return redirect()->route('civil2.cases.index')->with('success', 'Case created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create Civil2Case', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Failed to create case.');
        }
    }

    public function edit($id)
    {
        $case = $this->caseRepo->getById($id);
        if (!$case) {
            return redirect()->route('civil2.cases.index')->with('error', 'Case not found.');
        }

        return view('oag.civil2.cases.edit', [
            'case' => $case,
            'courtCategories' => $this->courtRepo->pluck(),
            'causesOfAction' => $this->causeRepo->pluck(),
            'caseStatuses' => $this->statusRepo->pluck(),
            'casePendingStatuses' => $this->pendingStatusRepo->pluck(),
            'caseOriginTypes' => $this->originRepo->pluck(),
            'counsels' => $this->userRepo->pluck()
        ]);
    }

    public function update(Request $request, $id)
    {
        Log::info('Civil2Case update request started', ['data' => $request->all(), 'id' => $id]);

        $data = $request->validate([
            'case_file_no' => 'required|string|unique:civil2_cases,case_file_no,' . $id,
            'court_case_no' => 'nullable|string|max:255',
            'case_name' => 'required|string|max:255',
            'date_received' => 'nullable|date',
            'date_opened' => 'required|date',
            'date_closed' => 'nullable|date|after_or_equal:date_opened',
            'court_type_id' => 'required|exists:court_categories,id',
            'cause_of_action_id' => 'required|exists:causes_of_action,id',
            'responsible_counsel_id' => 'required|exists:users,id',
            'case_status_id' => 'required|exists:case_statuses,id',
            'case_pending_status_id' => 'nullable|exists:case_pending_statuses,id',
            'case_origin_type_id' => 'required|exists:case_origin_types,id',
            'case_description' => 'nullable|string',
        ]);

        $data['updated_by'] = auth()->id();

        DB::beginTransaction();
        try {
            $case = $this->caseRepo->findOrFail($id);
            $case->update($data);

            DB::commit();

            Log::info('Civil2Case updated successfully', ['id' => $id]);
            return redirect()->route('civil2.cases.index')->with('success', 'Case updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update Civil2Case', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Failed to update case.');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $case = $this->caseRepo->getById($id);

            if (!$case) {
                return response()->json(['message' => 'Case not found.'], Response::HTTP_NOT_FOUND);
            }

            $case->delete();
            DB::commit();

            return response()->json(['message' => 'Case deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete Civil2Case', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to delete case.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
