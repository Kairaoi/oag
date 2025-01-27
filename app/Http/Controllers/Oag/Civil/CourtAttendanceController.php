<?php

namespace App\Http\Controllers\Oag\Civil;

use App\Http\Controllers\Controller;
use App\Repositories\Oag\Civil\CourtAttendanceRepository;
use App\Repositories\Oag\Civil\CivilCaseRepository;
use App\Repositories\Oag\Crime\UserRepository;
use Illuminate\Http\Request;
use App\Models\Oag\Civil\CourtAttendance;
use DataTables;
use DB;
use Illuminate\Support\Facades\Log;
class CourtAttendanceController extends Controller
{
    protected $courtAttendanceRepository;
    protected $civilCaseRepository;

    public function __construct(CivilCaseRepository $civilCaseRepository,CourtAttendanceRepository $courtAttendanceRepository, UserRepository $userRepository)
    {
        $this->courtAttendanceRepository = $courtAttendanceRepository;
        $this->civilCaseRepository = $civilCaseRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
  
    public function index()
    {
        return view('oag.civil.court_attendances.index');
    }

    public function getDataTables(Request $request)
    {
        $search = $request->input('search.value', '');
        $query = $this->courtAttendanceRepository->getForDataTable($search);
        return DataTables::of($query)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $civilCases = $this->civilCaseRepository->pluck();
        $lawyers = $this->userRepository->pluck();
        return view('oag.civil.court_attendances.create', compact('civilCases','lawyers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'civil_case_id' => 'required|exists:civil_cases,id',
            'counsels' => 'required|array',
            'counsels.*.user_id' => 'required|exists:users,id',
            'counsels.*.type' => 'required|in:Plaintiff,Defendant',
            'hearing_date' => 'required|date',
            'hearing_type' => 'nullable|string|max:255',
            'hearing_time' => 'nullable|date_format:H:i',
            'case_status' => 'required|in:Concluded,Ongoing,Adjourned,Other',
            'status_notes' => 'nullable|string',
        ]);
    
        // Get the opposing counsel's user_id from the counsels array
        $opposingCounsel = $data['counsels'][0]['user_id']; // Assuming the first counsel is the opposing one
    
        // Create the CourtAttendance record
        $courtAttendance = CourtAttendance::create([
            'civil_case_id' => $data['civil_case_id'],
            'hearing_date' => $data['hearing_date'],
            'hearing_type' => $data['hearing_type'],
            'hearing_time' => $data['hearing_time'],
            'case_status' => $data['case_status'],
            'status_notes' => $data['status_notes'],
            'opposing_counsel_name' => $opposingCounsel, // Set the opposing counsel here
        ]);
    
        // Attach counsels to the court attendance
       
    
        return redirect()->route('civil.courtattendance.index')->with('success', 'Court Attendance created successfully!');
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $courtAttendance = $this->courtAttendanceRepository->getById($id);
        $civilCases = $this->civilCaseRepository->pluck();

        return view('oag.civil.court_attendances.edit', compact('courtAttendance', 'civilCases'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
{
    // Validate incoming data based on the schema provided
    $data = $request->validate([
        'civil_case_id' => 'required|exists:civil_cases,id',  // Ensure the civil_case_id exists in the 'civil_cases' table
        'opposing_counsel_name' => 'required|string|max:255',
        'hearing_date' => 'required|date',  // Validates the date format
        'hearing_type' => 'nullable|string|max:255',  // Optional field for hearing type
        'hearing_time' => 'nullable|date_format:H:i',  // Validates time format (HH:MM)
        'case_status' => 'required|in:Concluded,Ongoing,Adjourned,Other',  // Enum values validation
        'status_notes' => 'nullable|string',  // Optional status notes field
    ]);

    // Use the repository to update the existing CourtAttendance record with the validated data
    $this->courtAttendanceRepository->update($id, $data);

    // Redirect back to the index page with a success message
    return redirect()->route('court_attendances.index')->with('success', 'Court attendance updated successfully.');
}

public function show($id)
{
    // Retrieve the court attendance record by ID
    $courtAttendance = $this->courtAttendanceRepository->getById($id);

    // If the record does not exist, redirect back with an error message
    if (!$courtAttendance) {
        return redirect()->route('court_attendances.index')->with('error', 'Court attendance not found.');
    }

    // Pass the retrieved record to the view
    return view('court_attendances.show', compact('courtAttendance'));
}

    /**
     * Get the count of CourtAttendance records.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        $totalCount = $this->courtAttendanceRepository->count();

        return response()->json(['count' => $totalCount]);
    }
}
