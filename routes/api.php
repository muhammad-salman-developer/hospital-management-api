<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\DoctorSheduleController;
use App\Http\Controllers\Api\EmailVerificationNotificationController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ResendVerificationController;
use App\Http\Controllers\Api\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes (bina login ke)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/email/resend', [ResendVerificationController::class, 'resend'])
    ->middleware('throttle:6,1');

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');
// Protected routes (sirf logged-in users)
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'send'])
        ->middleware('throttle:6,1');

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('departments', DepartmentController::class)
        ->middleware([
            'index' => 'permission:department-view',
            'show' => 'permission:department-view',
            'store' => 'permission:department-add',
            'update' => 'permission:department-update',
            'destroy' => 'permission:department-delete',
        ]);

    Route::apiResource('doctors', DoctorController::class)
        ->middleware([
            'index' => 'permission:doctor-view',
            'show' => 'permission:doctor-view',
            'store' => 'permission:doctor-add',
            'update' => 'permission:doctor-update',
            'destroy' => 'permission:doctor-delete',
        ]);

    Route::apiResource('doctor-shedules', DoctorSheduleController::class)
        ->middleware([
            'index' => 'permission:doctor-schedule-view',
            'show' => 'permission:doctor-schedule-view',
            'store' => 'permission:doctor-schedule-add',
            'update' => 'permission:doctor-schedule-update',
            'destroy' => 'permission:doctor-schedule-delete',
        ]);
    Route::apiResource('patients', PatientController::class)
        ->middleware([
            'index' => 'permission:patient-view',
            'show' => 'permission:patient-view',
            'store' => 'permission:patient-add',
            'update' => 'permission:patient-update',
            'destroy' => 'permission:patient-delete',
        ]);

});
