<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\DoctorSheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();    })->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
// departments
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
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
});
