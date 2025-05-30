<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IpController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordController;




Route::middleware('blacklist')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/verify-ip', [AuthController::class, 'verifyIp'])->name('verify-ip');

    
    Route::middleware(['auth:sanctum', 'whitelist'])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('passwords', [PasswordController::class, 'index']);
        Route::post('passwords', [PasswordController::class, 'store']);
        Route::get('passwords/{id}', [PasswordController::class, 'show']);
        Route::put('passwords/{id}', [PasswordController::class, 'update']);
        Route::delete('passwords/{id}', [PasswordController::class, 'destroy']);
        Route::post('/import-passwords', [PasswordController::class, 'importPasswords']);

        Route::post('/ips/whitelist', [IpController::class, 'whitelist']);
        Route::delete('/ips/whitelist/delete/{id}', [IpController::class, 'removeWhitelisted']);
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/ips', [IpController::class, 'index']);
        Route::post('/ips/blacklist', [IpController::class, 'blacklist']);
        Route::delete('/ips/blacklist/delete/{id}', [IpController::class, 'removeBlacklisted']);
    });
});
