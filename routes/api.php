<?php

use App\Http\Controllers\Api\AuthClientController;
use App\Http\Controllers\Api\AuthEmploieController;
use App\Http\Controllers\Api\V1\WalletController;
use Illuminate\Support\Facades\Route;

Route::post('/token', [AuthClientController::class, 'getToken']);
Route::delete('/token', [AuthClientController::class, 'deleteToken'])->middleware('auth:sanctum');

Route::post('/emploie/token', [AuthEmploieController::class, 'getToken']);
Route::delete('/emploie/token', [AuthEmploieController::class, 'deleteToken'])->middleware('auth:sanctum');

Route::name('apiv1.')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('wallets', WalletController::class);
});
