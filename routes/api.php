<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\ForoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RespuestaController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\UserController;


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class)->except('store');
    Route::apiResource('grupos', GrupoController::class);
    Route::apiResource('inscripciones', InscripcionController::class)
        ->parameters(['inscripciones' => 'inscripcion']);
    Route::apiResource('tareas', TareaController::class);
    Route::apiResource('entregas', EntregaController::class);
    Route::apiResource('materiales', MaterialController::class)
        ->parameters(['materiales' => 'material']);
    Route::apiResource('foros', ForoController::class);
    Route::apiResource('respuestas', RespuestaController::class);
});