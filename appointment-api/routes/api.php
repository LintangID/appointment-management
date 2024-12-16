<?php

use App\Http\Controllers\api\AppointmentController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::prefix('auth')->group(function () {
    Route::post('/login',[AuthController::class,'login']);
    Route::get('/me',[AuthController::class,'me'])->middleware(['auth:sanctum']);
    Route::get('/logout',[AuthController::class,'logout'])->middleware(['auth:sanctum']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('users', [UserController::class,'index']);
    Route::get('appointments', [AppointmentController::class,'index']);
    Route::post('appointments', [AppointmentController::class,'store']);
});
