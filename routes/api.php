<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// login api
Route::post('/auth/login', [AuthController::class, 'login']);

// Route Protected (Harus Login dengan Token)
Route::middleware('auth:sanctum')->group(function () {

    // Get User Profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Logout
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Absensi
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    // Nanti tambahkan check-out disini
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);

});
