<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\DoctorSheduleController;
use App\Http\Controllers\Api\EmailVerificationNotificationController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\PaymentController;
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

// Public: website par bina login ke doctors/departments/schedules dikhane ke liye
Route::get('/doctors', [DoctorController::class, 'index']);
Route::get('/doctors/{doctor}', [DoctorController::class, 'show']);
Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/departments/{department}', [DepartmentController::class, 'show']);
Route::get('/doctor-shedules', [DoctorSheduleController::class, 'index']);

// Protected routes (sirf logged-in users)
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'send'])
        ->middleware('throttle:6,1');

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/patient/profile', [PatientController::class, 'myProfile']);
    Route::middleware('auth:sanctum')->get('/my-patients', [PatientController::class, 'myPatients']);

    Route::apiResource('departments', DepartmentController::class)
        ->except(['index', 'show'])
        ->middleware([
            'store' => 'permission:department-add',
            'update' => 'permission:department-update',
            'destroy' => 'permission:department-delete',
        ]);

    Route::apiResource('doctors', DoctorController::class)
        ->except(['index', 'show'])
        ->middleware([
            'store' => 'permission:doctor-add',
            'update' => 'permission:doctor-update',
            'destroy' => 'permission:doctor-delete',
        ]);

    Route::apiResource('doctor-shedules', DoctorSheduleController::class)
        ->except(['index'])
        ->middleware([
            'show' => 'permission:doctor-schedule-view',
            'store' => 'permission:doctor-schedule-add',
            'update' => 'permission:doctor-schedule-update',
            'destroy' => 'permission:doctor-schedule-delete',
        ]);

    // Route::apiResource('patients', PatientController::class)
    //     ->middleware([
    //         'index' => 'permission:patient-view',
    //         'show' => 'permission:patient-view',
    //         // 'store' => 'permission:patient-add',
    //         'update' => 'permission:patient-update',
    //         'destroy' => 'permission:patient-delete',
    //     ]);
    Route::get('/patients', [PatientController::class, 'index'])
        ->middleware('permission:patient-view');

    Route::post('/patients', [PatientController::class, 'store']);

    Route::get('/patients/{patient}', [PatientController::class, 'show'])
        ->middleware('permission:patient-view');

    Route::put('/patients/{patient}', [PatientController::class, 'update'])
        ->middleware('permission:patient-update');

    Route::patch('/patients/{patient}', [PatientController::class, 'update'])
        ->middleware('permission:patient-update');

    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])
        ->middleware('permission:patient-delete');

    // Route::apiResource('appointments', AppointmentController::class)
    //     ->middleware([
    //         'index' => 'permission:appointment-view',
    //         'show' => 'permission:appointment-view',
    //         // 'store' => 'permission:appointment-add',
    //         'update' => 'permission:appointment-update',
    //         'destroy' => 'permission:appointment-delete',
    //     ]);
    Route::get('/appointments', [AppointmentController::class, 'index'])
        ->middleware('permission:appointment-view');

    Route::post('/appointments', [AppointmentController::class, 'store']);

    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])
        ->middleware('permission:appointment-view');

    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])
        ->middleware('permission:appointment-update');

    Route::patch('/appointments/{appointment}', [AppointmentController::class, 'update'])
        ->middleware('permission:appointment-update');

    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])
        ->middleware('permission:appointment-delete');
    Route::apiResource('payments', PaymentController::class)
        ->middleware([
            'index' => 'permission:payment-view',
            'show' => 'permission:payment-view',
            'store' => 'permission:payment-add',
            'update' => 'permission:payment-update',
            'destroy' => 'permission:payment-delete',
        ]);
});
