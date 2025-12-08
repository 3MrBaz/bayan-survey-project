<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SurveyorController;

Route::get('/', function () { return view('welcome'); })->name('home');

// ==========================
// AUTH PROFILE
// ==========================
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';


Route::middleware(['auth', 'role:surveyor'])->group(function () {

    Route::get('/my-surveys', [SurveyorController::class, 'viewMySurveys'])->name('my-surveys');

    // CREATE SURVEY
    Route::get('/adding-survey', [SurveyorController::class, 'viewAddingSurvey']);
    Route::post('/adding-survey', [SurveyorController::class, 'addingSurvey'])->name('addingSurvey');

    // ADD QUESTIONS TO SURVEY PAGE
    Route::get('/adding-questions/{survey_id}', [SurveyorController::class, 'addingQuestions'])
        ->name('adding-questions');

    // STORE NEW QUESTION
    Route::post('/adding-question', [SurveyorController::class, 'storeQuestion'])
        ->name('store-question');

    // Add selected template questions
    Route::post('/add-selected-questions', [SurveyorController::class, 'addSelectedQuestions'])
        ->name('add-selected-questions');

    // AJAX fetch by category
    Route::get('/questions-by-category/{category}', [SurveyorController::class, 'getQuestionsByCategory']);

    // Delete category
    Route::delete('/delete-category', [SurveyorController::class, 'deleteCategory'])->name('delete-category');

    // Delete a question
    Route::delete('/delete-question/{id}', [SurveyorController::class, 'deleteQuestion'])
        ->name('delete-question');

    // Remove a question from survey
    Route::delete('/questions/{id}', [SurveyorController::class, 'removeQuestionFromSurvey'])
        ->name('remove-question-from-survey');

    // ANALYSIS
    Route::get('/analysis/{survey_id}', [SurveyorController::class, 'index'])->name('analysis');

    // IMPORT
    Route::post('/import-questions', [SurveyorController::class, 'importQuestions'])->name('import-questions');
    Route::get('/import-questions', [SurveyorController::class, 'viewImportQuestions'])->name('view-import-questions');

    // RESULTS
    Route::get('/survey/{survey_id}/results', [SurveyorController::class, 'results'])
        ->name('survey.results');

    // TOGGLE VISIBILITY
    Route::patch('/survey/{id}/toggle-visibility',[SurveyorController::class, 'toggleVisibility'])
        ->name('survey.toggle_visibility');

    // DELETE survey
    Route::delete('/surveys/{id}', [SurveyorController::class, 'destroy'])
        ->name('surveys.destroy');

    // SAVE RANDOM QUESTION COUNT
    Route::put('/survey/random-count', [SurveyorController::class, 'updateRandomCount'])
        ->name('update-random-question-count');

    
    Route::get('/export', [SurveyorController::class, 'exportPage'])->name('export.index');
    Route::get('/export/download', [SurveyorController::class, 'export'])->name('export.file');
    Route::post('/manual-grade', [SurveyorController::class, 'manualGrade'])->name('manual-grade');  
    
    Route::post('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');

});



// ==================================
// PUBLIC SURVEY ACCESS
// ==================================

Route::get('/survey-access/{survey_id}', [SurveyorController::class, 'surveyAccess'])
    ->name('survey.access');

Route::post('/survey-access/{survey_id}/check', [SurveyorController::class, 'checkSurveyPassword'])
    ->name('survey.check');

// ANSWERING PAGE (ONLY ONE ROUTE!)
Route::get('/answering-questions/{survey_id}', [SurveyorController::class, 'viewAnsweringPage'])
    ->name('answering-questions')
    ->middleware('auth');

// SUBMIT ANSWERS
Route::post('/survey/{survey_id}/submit', [SurveyorController::class, 'submit'])
    ->name('survey.submit')
    ->middleware('auth');

// AJAX SURVEY SEARCH
Route::get('/surveys-ajax', [UserController::class, 'ajaxSearch'])->name('surveys.ajax');

// PUBLIC SURVEYS LIST
Route::get('/surveys', [UserController::class, 'viewSurveys'])->name('surveys');

