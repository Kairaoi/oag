<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Oag\Crime\CrimeBoardController;
use App\Http\Controllers\Oag\Crime\CriminalCaseController;
use App\Http\Controllers\Oag\Crime\OffenceController;
use App\Http\Controllers\Oag\Crime\OffenceCategoryController;
use App\Http\Controllers\Oag\Crime\AccusedController;
use App\Http\Controllers\Oag\Crime\IslandController;
// use App\Http\Controllers\Oag\Crime\CouncilController;
use App\Http\Controllers\Oag\Crime\VictimController;
use App\Http\Controllers\Oag\Crime\ReasonsForClosureController;
use App\Http\Controllers\Oag\Crime\IncidentController;
use App\Http\Controllers\Oag\Crime\ReportController;




use App\Http\Controllers\Oag\Civil\CivilBoardController;
use App\Http\Controllers\Oag\Civil\CourtCategoryController;
use App\Http\Controllers\Oag\Civil\CaseTypeController;
use App\Http\Controllers\Oag\Civil\CivilCaseController;
use App\Http\Controllers\Oag\Civil\CourtAttendanceController;


use App\Http\Controllers\OAG\Legal\LegalTaskController;

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

    Route::match(['get', 'post'], 'victim/datatables', [VictimController::class, 'getDataTables'])->name('victim.datatables');
    Route::resource('victim', VictimController::class);

    Route::match(['get', 'post'], 'reason/datatables', [ReasonsForClosureController::class, 'getDataTables'])->name('reason.datatables');
    Route::resource('reason', ReasonsForClosureController::class);

    Route::match(['get', 'post'], 'incident/datatables', [IncidentController::class, 'getDataTables'])->name('incident.datatables');
    Route::resource('incident', IncidentController::class);

    Route::get('data-tables', [ReportController::class, 'index'])->name('reports.index');
    Route::get('execute-report/{reportId}', [ReportController::class, 'executeReport'])->name('executeReport');
    Route::get('show-results/{reportId}', [ReportController::class, 'showResults'])->name('showResults');
    Route::get('pims-dashboard/{reportId}', [ReportController::class, 'pimsDashboard'])->name('dashboard');
    

 
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

