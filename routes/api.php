<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GeocercaController;
use App\Http\Controllers\Api\AsistenciaController;
use App\Http\Controllers\Api\CalificacionController;
use App\Http\Controllers\Api\NotificacionController;
use App\Http\Controllers\Api\AsignacionController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/auth/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/geocercas', [GeocercaController::class, 'index']);
    Route::post('/geocercas', [GeocercaController::class, 'store']);
    Route::put('/geocercas/{id}', [GeocercaController::class, 'update']);
    Route::delete('/geocercas/{id}', [GeocercaController::class, 'destroy']);

    Route::post('/asistencia/register', [AsistenciaController::class, 'register']);
    Route::get('/asistencia/history', [AsistenciaController::class, 'history']);

    Route::get('/asignaciones', [AsignacionController::class, 'index']);
    Route::get('/calificaciones', [CalificacionController::class, 'index']);
    Route::get('/calificaciones/estudiante/{idEstudiante}', [CalificacionController::class, 'byStudent']);

    Route::get('/notificaciones', [NotificacionController::class, 'index']);
    Route::put('/notificaciones/{id}/read', [NotificacionController::class, 'markRead']);
    Route::get('/notificaciones/unread-count', [NotificacionController::class, 'unreadCount']);
});
