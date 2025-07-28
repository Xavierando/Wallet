<?php

use App\Http\Controllers\Api\AuthClientController;
use App\Http\Controllers\Api\AuthEmploieController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\WalletController;
use Illuminate\Support\Facades\Route;

Route::post('/token', [AuthClientController::class, 'getToken'])->name('api.login');
Route::delete('/token', [AuthClientController::class, 'deleteToken'])->middleware('auth:sanctum')->name('api.logout');

Route::post('/emploie/token', [AuthEmploieController::class, 'getToken'])->name('api.emploie.login');
Route::delete('/emploie/token', [AuthEmploieController::class, 'deleteToken'])->middleware('auth:sanctum')->name('api.emploie.logout');

Route::name('apiv1.')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('wallets', WalletController::class);
    Route::apiResource('wallets.transactions', TransactionController::class)->only(['index', 'store', 'show']);
});
