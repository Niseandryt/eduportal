<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GrupoController;


Route::post('login', [AuthController::class, 'login']);   

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('grupos', GrupoController::class);
    Route::post('logout', [AuthController::class, 'logout']);
});