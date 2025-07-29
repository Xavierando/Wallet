<?php

use App\Http\Controllers\Api\AuthClientController;
use App\Http\Controllers\Api\AuthEmployeeController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\WalletController;
use Illuminate\Support\Facades\Route;

Route::post('/token', [AuthClientController::class, 'getToken'])->name('api.login');
Route::delete('/token', [AuthClientController::class, 'deleteToken'])->middleware('auth:sanctum')->name('api.logout');

Route::post('/employee/token', [AuthEmployeeController::class, 'getToken'])->name('api.employee.login');
Route::delete('/employee/token', [AuthEmployeeController::class, 'deleteToken'])->middleware('auth:sanctum')->name('api.employee.logout');

Route::name('apiv1.')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('wallets', WalletController::class);
    Route::apiResource('wallets.transactions', TransactionController::class)->only(['index', 'store', 'show']);
});
