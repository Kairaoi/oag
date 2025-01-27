<?php

namespace App\Http\Controllers\Oag\Civil;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Civil\CivilCaseRepository;
use App\Repositories\Oag\Civil\CourtCategoryRepository;
use App\Repositories\Oag\Civil\CaseTypeRepository;
use App\Repositories\Oag\Crime\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use DataTables;
use Carbon\Carbon;
use App\Models\Oag\Civil\CivilCase;
use App\Models\Oag\Civil\CaseCounsel;


class CivilCaseController extends Controller
{
    protected $civilCaseRepository;
    protected $courtCategoryRepository;
    protected $caseTypeRepository;
    protected $userRepository;

    public function __construct(
        CivilCaseRepository $civilCaseRepository,
        CourtCategoryRepository $courtCategoryRepository,
        CaseTypeRepository $caseTypeRepository,
        UserRepository $userRepository
    ) {
        $this->civilCaseRepository = $civilCaseRepository;
        $this->courtCategoryRepository = $courtCategoryRepository;
        $this->caseTypeRepository = $caseTypeRepository;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        return view('oag.civil.civil_cases.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->civilCaseRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    public function create()
    {
        $courtCategories = $this->courtCategoryRepository->pluck();
        $caseTypes = $this->caseTypeRepository->pluck();
        $lawyers = $this->userRepository->pluck();
        
        return view('oag.civil.civil_cases.create', compact(
            'courtCategories',
            'caseTypes',
            'lawyers'
        ));
    }

    public function store(Request $request)
    {
        Log::info('Store Civil Case request initiated', [
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
        ]);

        // Validate the request
        $data = $request->validate([
            'court_category_id' => 'required|exists:court_categories,id',
            'case_type_id' => 'required|exists:case_types,id',
            'primary_number' => 'nullable|string|max:255',
            'number' => 'nullable|integer',
            'year' => 'required|integer',
            'case_name' => 'required|string',
            'case_description' => 'nullable|string',
            'current_status' => 'required|string|max:255',
            'status_date' => 'required|date',
            'action_required' => 'required|string|max:255',
            'monitoring_status' => 'required|in:Active,Pending,Closed',
            'entered_by_sg_dsg' => 'required|boolean',
            'counsels' => 'required|array',
            'counsels.*.user_id' => 'required|exists:users,id',
            'counsels.*.type' => 'required|in:Plaintiff,Defendant',
        ]);

        DB::beginTransaction();

        try {
            // Generate case number if not provided
            if (empty($data['primary_number'])) {
                $data['primary_number'] = $this->generateCaseNumber($data);
            }

            // Set created_by
            $data['created_by'] = auth()->id();

            // Create the civil case
            $civilCase = $this->civilCaseRepository->create($data);

            // Create case counsels
            foreach ($data['counsels'] as $counsel) {
                CaseCounsel::create([
                    'civil_case_id' => $civilCase->id,  // Ensure 'civil_case_id' is used
                    'user_id' => $counsel['user_id'],
                    'type' => $counsel['type'],
                    'created_by' => auth()->id()
                ]);
            }

            DB::commit();

            Log::info('Civil case created successfully', [
                'civil_case_id' => $civilCase->id,
                'created_by' => auth()->id(),
            ]);

            return redirect()
                ->route('civil.civilcase.index')
                ->with('success', 'Civil case created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating civil case:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create civil case.');
        }
    }

    public function show($id)
    {
        $civilCases = $this->civilCaseRepository->getById($id);
    
        if (!$civilCases) {
            return redirect()
                ->route('civil.civilcase.index')
                ->with('error', 'Civil case not found.');
        }
    
        return view('oag.civil.civil_cases.show', compact('civilCases'));
    }
    
    public function edit($id)
{
    // Retrieve the civil case by its ID
    $civilCase = $this->civilCaseRepository->getById($id);

    // Fetch the necessary data for select fields (court categories, case types, and lawyers)
    $courtCategories = $this->courtCategoryRepository->pluck();
    $caseTypes = $this->caseTypeRepository->pluck();
    $lawyers = $this->userRepository->pluck();

    // Check if the civil case exists
    if (!$civilCase) {
        return redirect()->route('civil.civilcase.index')->with('error', 'Civil case not found.');
    }

    // Pass the case data and the select field data to the view
    return view('oag.civil.civil_cases.edit', compact(
        'civilCase',
        'courtCategories',
        'caseTypes',
        'lawyers'
    ));
}


    public function update(Request $request, $id)
    {
        Log::info('Update Civil Case request initiated', [
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
        ]);
    
        // Validate the request
        $data = $request->validate([
            'court_category_id' => 'required|exists:court_categories,id',
            'case_type_id' => 'required|exists:case_types,id',
            'primary_number' => 'nullable|string|max:255',
            'number' => 'nullable|integer',
            'year' => 'required|integer',
            'case_name' => 'required|string',
            'case_description' => 'nullable|string',
            'current_status' => 'required|string|max:255',
            'status_date' => 'required|date',
            'action_required' => 'required|string|max:255',
            'monitoring_status' => 'required|in:Active,Pending,Closed',
            'entered_by_sg_dsg' => 'required|boolean',
            'counsels' => 'required|array',
            'counsels.*.user_id' => 'required|exists:users,id',
            'counsels.*.type' => 'required|in:Plaintiff,Defendant',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Fetch the existing civil case
            $civilCase = $this->civilCaseRepository->findOrFail($id);
    
            // If primary_number is not provided, don't change the existing one
            if (empty($data['primary_number'])) {
                $data['primary_number'] = $civilCase->primary_number;  // Retain the existing number if not updated
            }
    
            // Update the existing civil case
            $civilCase->update($data);
    
            // Remove old counsels and add the new ones
            // First, delete all existing counsels related to this case
            CaseCounsel::where('civil_case_id', $civilCase->id)->delete();
    
            // Add new counsels
            foreach ($data['counsels'] as $counsel) {
                CaseCounsel::create([
                    'civil_case_id' => $civilCase->id,
                    'user_id' => $counsel['user_id'],
                    'type' => $counsel['type'],
                    'created_by' => auth()->id(),
                ]);
            }
    
            DB::commit();
    
            Log::info('Civil case updated successfully', [
                'civil_case_id' => $civilCase->id,
                'updated_by' => auth()->id(),
            ]);
    
            return redirect()
                ->route('civil.civilcase.index')
                ->with('success', 'Civil case updated successfully.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating civil case:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update civil case.');
        }
    }
    

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $civilCase = $this->civilCaseRepository->getById($id);
            
            if (!$civilCase) {
                return response()->json([
                    'message' => 'Civil case not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Delete related counsels
            $civilCase->counsels()->delete();
            
            // Delete the civil case
            $civilCase->delete();

            DB::commit();

            return response()->json([
                'message' => 'Civil case deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting civil case:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to delete civil case'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function generateCaseNumber($data)
    {
        $courtCategory = $this->courtCategoryRepository->getById($data['court_category_id']);
        $year = substr($data['year'], -2);
        
        if ($courtCategory->name === 'High Court') {
            if ($data['case_type_id'] == 1) { // Assuming 1 is for Lit cases
                return sprintf('Lit %d/%s', $data['number'], $year);
            }
            return sprintf('HCCiv %05d/%s', $data['number'], $year);
        }
        
        return sprintf('MM %02d/%s', $data['number'], $year);
    }
}