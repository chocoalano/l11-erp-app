<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\JobPositionController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::get('profile', [AuthenticationController::class, 'profile'])->middleware('auth:sanctum');
    Route::post('profile', [AuthenticationController::class, 'profile_update'])->middleware('auth:sanctum');
    Route::post('logout', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum');
});
Route::middleware('auth:sanctum')->group( function () {
    Route::resource('/attendance', AttendanceController::class);
});
Route::resource('/users', UserController::class);
Route::resource('/organizations', OrganizationController::class);
Route::resource('/job-position', JobPositionController::class);
Route::post('sync-attendance', [AttendanceController::class, 'integratedFromMachine']);
