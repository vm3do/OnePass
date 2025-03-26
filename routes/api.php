<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function () {
    Route::get('passwords', [PasswordController::class, 'index']);
    Route::post('passwords', [PasswordController::class, 'store']);
    Route::get('passwords/{id}', [PasswordController::class, 'show']);
    Route::put('passwords/{id}', [PasswordController::class, 'update']);
    Route::delete('passwords/{id}', [PasswordController::class, 'destroy']);
});
