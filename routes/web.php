<?php

use App\Http\Controllers\AcademicPeriodController;
use App\Http\Controllers\AcademicRequestController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EarlyWarningController;
use App\Http\Controllers\FollowupinterventionController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\StudentActivityController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TuitionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/login', [SessionController::class, 'create'])->name('login');
Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:5,1');
Route::get('/setup', [SessionController::class, 'setup'])->name('setup');
Route::post('/setup', [SessionController::class, 'storeSetup'])->middleware('throttle:5,1');

Route::middleware('auth')->group(function () {
    Route::get('/manage', fn () => view('crud.index'))->name('crud.index');
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
    Route::apiResource('academic-periods', AcademicPeriodController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('students', StudentController::class);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('student-activities', StudentActivityController::class);
    Route::apiResource('academic-requests', AcademicRequestController::class);
    Route::apiResource('tuitions', TuitionController::class);
    Route::apiResource('early-warnings', EarlyWarningController::class);
    Route::apiResource('followup-interventions', FollowupinterventionController::class)
        ->only(['index', 'store', 'show']);
    Route::apiResource('users', UserController::class);
});
