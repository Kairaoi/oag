<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Oag\Crime\CrimeBoardController;
use App\Http\Controllers\Oag\Crime\CriminalCaseController;
use App\Http\Controllers\Oag\Crime\CaseReviewController;
use App\Http\Controllers\Oag\Crime\OffenceController;
use App\Http\Controllers\Oag\Crime\OffenceCategoryController;
use App\Http\Controllers\Oag\Crime\AccusedController;
use App\Http\Controllers\Oag\Crime\IslandController;
// use App\Http\Controllers\Oag\Crime\CouncilController;
use App\Http\Controllers\Oag\Crime\VictimController;
use App\Http\Controllers\Oag\Crime\ReasonsForClosureController;
use App\Http\Controllers\Oag\Crime\IncidentController;
use App\Http\Controllers\Oag\Crime\ReportController;
use App\Http\Controllers\Oag\Crime\CourtHearingController;
use App\Http\Controllers\Oag\Crime\CourtCaseController;

use App\Http\Controllers\Oag\Crime\AppealDetailController;


use App\Http\Controllers\Oag\Civil\CivilBoardController;
use App\Http\Controllers\Oag\Civil\CourtCategoryController;
use App\Http\Controllers\Oag\Civil\CaseTypeController;
use App\Http\Controllers\Oag\Civil\CivilCaseController;
use App\Http\Controllers\Oag\Civil\CourtAttendanceController;


use App\Http\Controllers\OAG\Legal\LegalTaskController;

use App\Http\Controllers\RolePermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::group([
    'as' => 'crime.',
    'prefix' => 'crime',
    'middleware' => ['auth'],
], function () {
    Route::resource('boards', \App\Http\Controllers\OAG\crime\CrimeBoardController::class, ['only' => ['index']]);

    Route::get('criminal-case/{id}/allocate', [CriminalCaseController::class, 'showAllocationForm'])
        ->name('criminalCase.allocateForm');
    Route::post('criminal-case/{id}/allocate', [CriminalCaseController::class, 'allocateLawyer'])
        ->name('criminalCase.allocateLawyer');

    Route::match(['get', 'post'], 'CaseReview/datatables', [CaseReviewController::class, 'getDataTables'])->name('CaseReview.datatables');
    Route::get('CaseReview/{id}/create', [CaseReviewController::class, 'create'])->name('CaseReview.create');
    // Route to store case review data
Route::post('CaseReview/store', [CaseReviewController::class, 'store'])->name('CaseReview.store');

// You can also use a resource route if you want to handle all the standard actions:
Route::resource('CaseReview', CaseReviewController::class)->except(['create', 'store']);

    Route::get('crime/criminalCase/{id}/createAccused', [App\Http\Controllers\Oag\Crime\CriminalCaseController::class, 'createAccused'])
    ->name('criminalCase.createAccused');

    Route::match(['get', 'post'], 'criminalCase/datatables', [CriminalCaseController::class, 'getDataTables'])->name('criminalCase.datatables');
    Route::resource('criminalCase', CriminalCaseController::class);

    Route::match(['get', 'post'], 'offence/datatables', [OffenceController::class, 'getDataTables'])->name('offence.datatables');
    Route::resource('offence', OffenceController::class);

    Route::match(['get', 'post'], 'category/datatables', [OffenceCategoryController::class, 'getDataTables'])->name('category.datatables');
    Route::resource('category', OffenceCategoryController::class);

    Route::match(['get', 'post'], 'accused/datatables', [AccusedController::class, 'getDataTables'])->name('accused.datatables');
    Route::resource('accused', AccusedController::class);

    Route::match(['get', 'post'], 'island/datatables', [IslandController::class, 'getDataTables'])->name('island.datatables');
    Route::resource('island', IslandController::class);

    // Route::match(['get', 'post'], 'council/datatables', [CouncilController::class, 'getDataTables'])->name('council.datatables');
    // Route::resource('council', CouncilController::class);
    Route::get('criminalCase/{id}/create-victim', [App\Http\Controllers\Oag\Crime\CriminalCaseController::class, 'createVictim'])
    ->name('criminalCase.createVictim');
    Route::match(['get', 'post'], 'victim/datatables', [VictimController::class, 'getDataTables'])->name('victim.datatables');
    Route::resource('victim', VictimController::class);
    Route::get('crime/appealcase/{id}', [CriminalCaseController::class, 'showAppealCases'])->name('appealcase');
    Route::get('crime/courtcase/{id}', [CriminalCaseController::class, 'showCourtCases'])->name('courtcase');
    Route::get('crime/casereview/{id}', [CriminalCaseController::class, 'showReviewedCases'])->name('casereview.reviewed');
    Route::get('crime/criminalCase/{id}/create-incident', [CriminalCaseController::class, 'createIncident'])
        ->name('criminalCase.createIncident');
    Route::match(['get', 'post'], 'reason/datatables', [ReasonsForClosureController::class, 'getDataTables'])->name('reason.datatables');
    Route::resource('reason', ReasonsForClosureController::class);

    Route::get('appeal/create/{id?}', [AppealDetailController::class, 'create'])
->name('appeal.create');
    Route::match(['get', 'post'], 'appeal/datatables', [AppealDetailController::class, 'getDataTables'])->name('appeal.datatables');
    Route::resource('appeal', AppealDetailController::class);

    Route::match(['get', 'post'], 'incident/datatables', [IncidentController::class, 'getDataTables'])->name('incident.datatables');
    Route::resource('incident', IncidentController::class);

    // 🔍 Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{id}', [ReportController::class, 'show'])->name('reports.show');




// Add new criminal case accept/reject routes here
Route::post('criminalCase/{id}/accept', [CriminalCaseController::class, 'accept'])->name('criminalCase.accept');
Route::post('criminalCase/{id}/reject', [CriminalCaseController::class, 'reject'])->name('criminalCase.reject');

Route::post('criminalCase/appeal/store', [CriminalCaseController::class, 'storeAppeal'])
->name('criminalCase.storeAppeal');

// Ngkana ko kainnanoia te appeal datatables route

Route::match(['get', 'post'], 'criminalCase/appeal/datatables', [CriminalCaseController::class, 'getAppealDataTables'])
->name('criminalCase.appealDatatables');
Route::post('criminalCase/{id}/reallocate', [CriminalCaseController::class, 'reallocateCase'])
->name('criminalCase.reallocate');
// Show reallocation form
Route::get('criminalCase/{id}/reallocate', [CriminalCaseController::class, 'showReallocationForm'])
    ->name('criminalCase.showReallocationForm');


// Court Hearings Routes
Route::match(['get', 'post'], 'court-hearings/datatables', [CourtHearingController::class, 'getDataTables'])->name('court-hearings.datatables');
Route::resource('court-hearings', CourtHearingController::class);

// Court Case DataTables route
Route::match(['get', 'post'], 'court-cases/datatables', [CourtCaseController::class, 'getDataTables'])->name('court-cases.datatables');
Route::get('CourtCase/{id}/create', [CourtCaseController::class, 'create'])->name('CourtCase.create');

// Court Case CRUD routes
Route::resource('court-cases',CourtCaseController::class);


// Court of Appeal Routes
Route::get('courtOfAppeal/create/{caseId?}', [\App\Http\Controllers\Oag\Crime\CourtOfAppealController::class, 'create'])
    ->name('courtOfAppeal.create');

Route::match(['get', 'post'], 'courtOfAppeal/datatables', [\App\Http\Controllers\Oag\Crime\CourtOfAppealController::class, 'getDataTables'])
    ->name('courtOfAppeal.datatables');

Route::resource('courtOfAppeal', \App\Http\Controllers\Oag\Crime\CourtOfAppealController::class)->except(['create']);

 
});

Route::group([
    'as' => 'civil.',
    'prefix' => 'civil',
    'middleware' => ['auth'],
], function () {
    Route::resource('boards', \App\Http\Controllers\OAG\Civil\CivilBoardController::class, ['only' => ['index']]);

  
    Route::match(['get', 'post'], 'courtcategory/datatables', [CourtCategoryController::class, 'getDataTables'])->name('courtcategory.datatables');
    Route::resource('courtcategory', CourtCategoryController::class);

    Route::match(['get', 'post'], 'casetype/datatables', [CaseTypeController::class, 'getDataTables'])->name('casetype.datatables');
    Route::resource('casetype', CaseTypeController::class);

    Route::match(['get', 'post'], 'civilcase/datatables', [CivilCaseController::class, 'getDataTables'])->name('civilcase.datatables');
    Route::resource('civilcase', CivilCaseController::class);

    Route::match(['get', 'post'], 'courtattendance/datatables', [CourtAttendanceController::class, 'getDataTables'])->name('courtattendance.datatables');
    Route::resource('courtattendance', CourtAttendanceController::class);
    
    

 
});



Route::group([
    'as' => 'legal.',
    'prefix' => 'legal',
    'middleware' => ['auth'],
], function () {

    Route::resource('boards', \App\Http\Controllers\OAG\Legal\LegalBoardController::class, ['only' => ['index']]);
    Route::match(['get', 'post'], 'legal_tasks/datatables', [LegalTaskController::class, 'getDataTables'])->name('legal_tasks.datatables');
    Route::resource('legal_tasks', LegalTaskController::class);
});


Route::group([
    'as' => 'draft.',  // Prefix for the draft-related routes
    'prefix' => 'draft',  // URL will start with '/draft'
    'middleware' => ['auth'],  // Ensures that only authenticated users can access these routes
], function () {

    // Draft Boards Route (assuming the controller is 'DraftBoardController')
    Route::resource('boards', \App\Http\Controllers\OAG\Draft\DraftBoardController::class, ['only' => ['index']]);

    // Ministry Routes (assuming the controller is 'MinistryController')
    Route::match(['get', 'post'], 'ministry/datatables', [\App\Http\Controllers\OAG\Draft\MinistryController::class, 'getDataTables'])->name('ministry.datatables');
    Route::resource('ministry', \App\Http\Controllers\OAG\Draft\MinistryController::class);

    Route::match(['get', 'post'], 'bills/datatables', [\App\Http\Controllers\OAG\Draft\BillController::class, 'getDataTables'])->name('bills.datatables');

    // Bill Routes (assuming the controller is 'BillController')
    Route::resource('bills', \App\Http\Controllers\OAG\Draft\BillController::class);

    // Bill Counsel Routes (assuming the controller is 'BillCounselController')
    Route::resource('bill_counsels', \App\Http\Controllers\OAG\Draft\BillCounselController::class);

    Route::match(['get', 'post'], 'counsels/datatables', [\App\Http\Controllers\OAG\Draft\CounselController::class, 'getDataTables'])->name('counsels.datatables');

    // Counsel Routes (assuming the controller is 'CounselController')
    Route::resource('counsels', \App\Http\Controllers\OAG\Draft\CounselController::class);

    Route::match(['get', 'post'], 'regulations/datatables', [\App\Http\Controllers\OAG\Draft\CounselController::class, 'getDataTables'])->name('regulations.datatables');

    // Regulation Routes (assuming the controller is 'RegulationController')
    Route::resource('regulations', \App\Http\Controllers\OAG\Draft\RegulationController::class);

    // Regulation Counsel Routes (assuming the controller is 'RegulationCounselController')
    Route::resource('regulation_counsels', \App\Http\Controllers\OAG\Draft\RegulationCounselController::class);
});





Route::group([
    'as' => 'admin.',
    'prefix' => 'admin',
    'middleware' => ['auth'],
], function () {
    Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
    Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
    Route::post('/assign-role', [RolePermissionController::class, 'assignRole'])->name('roles.assign');
});


use App\Http\Controllers\Oag\Civil2\{
    DashboardController,
    // CounselController,
    CaseController,
    // CasePartyController,
    CaseActivityController,
    CaseStatusController,
    CaseClosureController,
    QuarterlyReportController,
    ReportsController
};

Route::group([
    'as' => 'civil2.',
    'prefix' => 'civil2',
    'middleware' => ['auth'],
], function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Counsel management
    // Route::resource('counsels', CounselController::class);

    Route::get('cases/{case}/review', [CaseController::class, 'review'])->name('cases.review');
    
    // Case management
    Route::match(['get', 'post'], 'case/datatables', [CaseController::class, 'getDataTables'])->name('case.datatables');
    Route::resource('cases', CaseController::class);
    Route::get('cases/{case}/timeline', [CaseController::class, 'timeline'])->name('cases.timeline');
    
    // Case parties
    // Route::resource('cases.parties', CasePartyController::class);
    
    // Case activities
    Route::resource('cases.activities', CaseActivityController::class);
    
    // Case status updates
    Route::post('cases/{case}/status', [CaseStatusController::class, 'update'])->name('cases.status.update');
    
    // Case closures
    // Show the closure form
    Route::get('cases/{case}/close', [CaseClosureController::class, 'create'])->name('close.create');

    // Handle form submission to create a closure
    Route::post('cases/{case}/close', [CaseClosureController::class, 'store'])->name('close.store');

    // Show a specific closure record
    Route::get('cases/{case}/closure/{closure}', [CaseClosureController::class, 'show'])->name('closures.show');

    // Reopen a previously closed case
    Route::post('cases/{case}/reopen', [CaseClosureController::class, 'reopen'])->name('close.reopen');

    // Explicit route for closing a case directly (not via form)
    Route::post('cases/{case}/force-close', [CaseClosureController::class, 'close'])->name('close.force');

    // Optional: Resource routes (for RESTful closure operations if needed)
    Route::resource('cases.closures', CaseClosureController::class)->only(['index', 'destroy', 'update']);

    
    // Quarterly reports
    Route::resource('quarterly-reports', QuarterlyReportController::class);
    Route::post('quarterly-reports/{quarterlyReport}/submit', [QuarterlyReportController::class, 'submit'])->name('quarterly-reports.submit');
    
    // Reports and analytics
    Route::get('reports/case-status', [ReportsController::class, 'caseStatus'])->name('reports.case-status');
    Route::get('reports/counsel-workload', [ReportsController::class, 'counselWorkload'])->name('reports.counsel-workload');
    Route::get('reports/case-types', [ReportsController::class, 'caseTypes'])->name('reports.case-types');
});
