<?php

namespace App\Http\Controllers\Oag\Civil2;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Civil2\CaseRepository;
use App\Repositories\Oag\Civil\CourtCategoryRepository;
use App\Repositories\Oag\Civil2\CauseOfActionRepository;
use App\Repositories\Oag\Civil2\CaseStatusRepository;
use App\Repositories\Oag\Civil2\CasePendingStatusRepository;
use App\Repositories\Oag\Civil2\CaseOriginTypeRepository;
use App\Repositories\Oag\Civil2\ExternalCounselRepository;
use App\Repositories\Oag\Crime\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Models\Oag\Civil2\Civil2Case;
use Illuminate\Support\Arr; // Added for Arr::only
use DataTables;

class CaseController extends Controller
{
    protected $externalCounselRepo,$caseRepo, $courtRepo, $causeRepo, $statusRepo, $pendingStatusRepo, $originRepo, $userRepo;

    public function __construct(
        CaseRepository $caseRepo,
        CourtCategoryRepository $courtRepo,
        CauseOfActionRepository $causeRepo,
        CaseStatusRepository $statusRepo,
        CasePendingStatusRepository $pendingStatusRepo,
        CaseOriginTypeRepository $originRepo,
        UserRepository $userRepo,
         ExternalCounselRepository $externalCounselRepo
    ) {
        $this->caseRepo = $caseRepo;
        $this->courtRepo = $courtRepo;
        $this->causeRepo = $causeRepo;
        $this->statusRepo = $statusRepo;
        $this->pendingStatusRepo = $pendingStatusRepo;
        $this->originRepo = $originRepo;
        $this->userRepo = $userRepo;
          $this->externalCounselRepo = $externalCounselRepo;
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
       
        
        'caseOriginTypes' => $this->originRepo->pluck(),
        'internalCounsels' => $this->userRepo->pluck(),
        'externalCounsels' => $this->externalCounselRepo->pluck() 
    ]);
}


public function store(Request $request)
{
    Log::info('Civil2Case store request started', ['data' => $request->all()]);

    // Validate main case fields
    $caseData = $request->validate([
        'case_origin_type_id' => 'required|exists:case_origin_types,id',
        'court_type_id' => 'required|exists:court_categories,id',
        'case_file_no' => 'required|string|max:255',
        'case_name' => 'required|string|max:255',
        'court_case_no' => 'nullable|string|max:255',
        'date_received' => 'required|date',
        'date_opened' => 'required|date',
        'cause_of_action_id' => 'required|exists:causes_of_action,id',
        // 'case_pending_status_id' => 'nullable|exists:case_pending_statuses,id',
        // 'case_description' => 'nullable|string',
        'responsible_counsel_id' => 'nullable|exists:users,id',
        
        // Add case_status fields here
        'status_date' => 'required|date',
        'current_status' => 'required|string',
        'action_required' => 'nullable|string',
        'monitoring_status' => 'nullable|string',
    ]);

    // Validate counsel input
    $request->validate([
        'counsels' => 'nullable|array',
        'counsels.*.id' => 'required',
        'counsels.*.type' => 'required|in:user,external',
        'counsels.*.role' => 'required|in:plaintiff,defendant',
    ]);

    Log::info('Civil2Case validation passed', [
        'main_case' => $caseData,
        'counsels' => $request->counsels ?? [],
    ]);

    $caseData['created_by'] = auth()->id();

    DB::beginTransaction();
    try {
        // Create the main case
        $case = $this->caseRepo->create($caseData);
        Log::info('Civil2Case record created', ['case_id' => $case->id]);

        // Save related case status (manual entry)
        $case->statuses()->create([
            'status_date' => $caseData['status_date'],
            'current_status' => $caseData['current_status'],
            'action_required' => $caseData['action_required'],
            'monitoring_status' => $caseData['monitoring_status'],
        ]);
        Log::info('CaseStatus created', ['case_id' => $case->id]);

        // External counsel helper
        $getOrCreateExternal = function ($counselData) {
            if (!empty($counselData['id'])) {
                $external = \App\Models\Oag\Civil2\ExternalCounsel::find($counselData['id']);
                if ($external) {
                    Log::info('External counsel retrieved by ID', ['counsel' => $external]);
                    return $external;
                }
            }

            $external = \App\Models\Oag\Civil2\ExternalCounsel::create([
                'name' => $counselData['name'] ?? 'Unnamed External Counsel',
                'email' => $counselData['email'] ?? null,
                'phone' => $counselData['phone'] ?? null,
                'address' => $counselData['address'] ?? null,
            ]);

            Log::info('New external counsel created', ['counsel' => $external]);
            return $external;
        };

        // Save all counsels
        foreach ($request->counsels ?? [] as $index => $counsel) {
            $counselId = $counsel['type'] === 'external'
                ? $getOrCreateExternal($counsel)->id
                : $counsel['id'];

            $case->caseCounsels()->create([
                'counsel_id' => $counselId,
                'counsel_type' => $counsel['type'] === 'external'
                    ? \App\Models\Oag\Civil2\ExternalCounsel::class
                    : \App\Models\User::class,
                'role' => $counsel['role'],
            ]);

            Log::info('Counsel linked to case', [
                'case_id' => $case->id,
                'counsel_id' => $counselId,
                'type' => $counsel['type'],
                'role' => $counsel['role'],
                'index' => $index
            ]);
        }

        DB::commit();
        Log::info('Civil2Case created successfully with status and counsels', ['case_id' => $case->id]);

        return redirect()->route('civil2.cases.index')->with('success', 'Case created successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to create Civil2Case', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->withInput()->with('error', 'Failed to create case.');
    }
}




    public function edit($caseId)
    {
        // dd($caseId);

        $case = $this->caseRepo->getById($caseId);
        
        if (!$case) {
            return redirect()->route('civil2.cases.edit')->with('error', 'Case not found.');
        }

        // Load related data (e.g., statuses and counsels) to ensure relationships are available
        $case->load('statuses', 'caseCounsels');

        return view('oag.civil2.civil_cases.edit', [
            'case' => $case,
            'courtCategories' => $this->courtRepo->pluck(),
            'causesOfAction' => $this->causeRepo->pluck(),
            'caseOriginTypes' => $this->originRepo->pluck(),
            'internalCounsels' => $this->userRepo->pluck(),
            'externalCounsels' => $this->externalCounselRepo->pluck(),
            'casePendingStatuses' => $this->pendingStatusRepo->pluck()
        ]);
    }


public function update(Request $request, $caseId)
    {
        Log::info('Civil2Case update request started', ['case_id' => $caseId, 'data' => $request->all()]);

        // Validate main case fields
        $caseData = $request->validate([
            'case_origin_type_id' => 'required|exists:case_origin_types,id',
            'court_type_id' => 'required|exists:court_categories,id',
            'case_file_no' => 'required|string|max:255|unique:civil2_cases,case_file_no,' . $caseId,
            'case_name' => 'required|string|max:255',
            'court_case_no' => 'nullable|string|max:255',
            'date_received' => 'required|date',
            'date_opened' => 'required|date',
            'cause_of_action_id' => 'required|exists:causes_of_action,id',
            'responsible_counsel_id' => 'nullable|exists:users,id',
            'status_date' => 'required|date',
            'current_status' => 'required|string',
            'action_required' => 'nullable|string',
            'monitoring_status' => 'nullable|string',
            'activity_type' => 'nullable|string',
            'activity_description' => 'nullable|string',
            'case_pending_status_id' => 'nullable|exists:case_pending_statuses,id',
            'status_notes' => 'nullable|string',
        ]);

        // Validate counsel input
        $request->validate([
            'counsels' => 'nullable|array',
            'counsels.*.id' => 'required',
            'counsels.*.type' => 'required|in:user,external',
            'counsels.*.role' => 'required|in:plaintiff,defendant',
        ]);

        Log::info('Civil2Case validation passed', [
            'case_id' => $caseId,
            'main_case' => $caseData,
            'counsels' => $request->counsels ?? [],
        ]);

        $caseData['updated_by'] = auth()->id();

        DB::beginTransaction();
        try {
            // Find the existing case
            $case = $this->caseRepo->getById($caseId);
            if (!$case) {
                throw new \Exception('Case not found');
            }

            // Update the main case
            $this->caseRepo->update($caseId, Arr::only($caseData, [
                'case_origin_type_id',
                'court_type_id',
                'case_file_no',
                'case_name',
                'court_case_no',
                'date_received',
                'date_opened',
                'cause_of_action_id',
                'responsible_counsel_id',
                'updated_by',
            ]));
            Log::info('Civil2Case record updated', ['case_id' => $caseId]);

            // Create new case status entry
            $status = $case->statuses()->create([
                'status_date' => $caseData['status_date'],
                'current_status' => $caseData['current_status'],
                'action_required' => $caseData['action_required'],
                'monitoring_status' => $caseData['monitoring_status'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
            Log::info('CaseStatus created', ['case_id' => $caseId, 'status_id' => $status->id]);

            // Log status history
            $case->statusHistory()->create([
                'case_status_id' => $status->id,
                'case_pending_status_id' => $caseData['case_pending_status_id'],
                'notes' => $caseData['status_notes'] ?? 'Status updated',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
            Log::info('CaseStatusHistory created', ['case_id' => $caseId, 'status_id' => $status->id]);

            // Log activity if provided
            if ($request->filled('activity_type') && $request->filled('activity_description')) {
                $case->activities()->create([
                    'activity_type' => $caseData['activity_type'],
                    'activity_date' => $caseData['status_date'],
                    'description' => $caseData['activity_description'],
                    'performed_by' => auth()->id(),
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
                Log::info('CaseActivity created', ['case_id' => $caseId]);
            }

            // Update counsels
            $case->caseCounsels()->delete(); // Remove existing counsels
            $getOrCreateExternal = function ($counselData) {
                if (!empty($counselData['id'])) {
                    $external = \App\Models\Oag\Civil2\ExternalCounsel::find($counselData['id']);
                    if ($external) {
                        Log::info('External counsel retrieved by ID', ['counsel' => $external]);
                        return $external;
                    }
                }

                $external = \App\Models\Oag\Civil2\ExternalCounsel::create([
                    'name' => $counselData['name'] ?? 'Unnamed External Counsel',
                    'email' => $counselData['email'] ?? null,
                    'phone' => $counselData['phone'] ?? null,
                    'address' => $counselData['address'] ?? null,
                ]);

                Log::info('New external counsel created', ['counsel' => $external]);
                return $external;
            };

            foreach ($request->counsels ?? [] as $index => $counsel) {
                $counselId = $counsel['type'] === 'external'
                    ? $getOrCreateExternal($counsel)->id
                    : $counsel['id'];

                $case->caseCounsels()->create([
                    'counsel_id' => $counselId,
                    'counsel_type' => $counsel['type'] === 'external'
                        ? \App\Models\Oag\Civil2\ExternalCounsel::class
                        : \App\Models\User::class,
                    'role' => $counsel['role'],
                ]);

                Log::info('Counsel linked to case', [
                    'case_id' => $caseId,
                    'counsel_id' => $counselId,
                    'type' => $counsel['type'],
                    'role' => $counsel['role'],
                    'index' => $index,
                ]);
            }

            DB::commit();
            Log::info('Civil2Case updated successfully with status, history, and counsels', ['case_id' => $caseId]);

            return redirect()->route('civil2.cases.index')->with('success', 'Case updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update Civil2Case', [
                'case_id' => $caseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
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
