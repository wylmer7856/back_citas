<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\EspecialidadesController;
use App\Http\Controllers\MedicoEspecialidadController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\HistorialController;

// ----------------------------
// RUTAS PÚBLICAS (Auth)
// ----------------------------
Route::post('/register', [UsuariosController::class, 'register']);
Route::post('/login', [UsuariosController::class, 'login']);

// ----------------------------
// RUTAS PROTEGIDAS (Auth Sanctum)
// ----------------------------
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [UsuariosController::class, 'logout']);

    // ----------------------------
    // ADMIN
    // ----------------------------
    Route::middleware('role:admin')->group(function () {
        // Gestión de usuarios
        Route::get('/usuarios', [UsuariosController::class, 'index']); 
        Route::post('/usuarios', [UsuariosController::class, 'store']);
        Route::get('/usuarios/{id}', [UsuariosController::class, 'show']);
        Route::put('/usuarios/{id}', [UsuariosController::class, 'update']);
        Route::delete('/usuarios/{id}', [UsuariosController::class, 'destroy']);

        // Gestión de especialidades
        Route::post('/especialidades', [EspecialidadesController::class, 'store']);    
        Route::put('/especialidades/{id}', [EspecialidadesController::class, 'update']);
        Route::delete('/especialidades/{id}', [EspecialidadesController::class, 'destroy']);

        // Asignar especialidades a médicos
        Route::post('/medico-especialidad', [MedicoEspecialidadController::class, 'store']);
        Route::delete('/medico-especialidad/{id}', [MedicoEspecialidadController::class, 'destroy']);

        // Eliminar historiales
        Route::delete('/historial/{id}', [HistorialController::class, 'destroy']);
    });

    // ----------------------------
    // ADMIN + MEDICO
    // ----------------------------
    Route::middleware('role:admin,medico')->group(function () {
        // Ver especialidades
        Route::get('/especialidades', [EspecialidadesController::class, 'index']);
        Route::get('/especialidades/{id}', [EspecialidadesController::class, 'show']);

        // Ver relaciones médico-especialidad
        Route::get('/medico-especialidad', [MedicoEspecialidadController::class, 'index']);

        // Manejo de historiales médicos
        Route::get('/historial', [HistorialController::class, 'index']);
        Route::get('/historial/{id}', [HistorialController::class, 'show']);
        Route::post('/historial', [HistorialController::class, 'store']);
        Route::put('/historial/{id}', [HistorialController::class, 'update']);
    });

    // ----------------------------
    // MEDICO + PACIENTE
    // ----------------------------
    Route::middleware('role:medico,paciente')->group(function () {
        // Manejo de citas
        Route::get('/citas', [CitasController::class, 'index']);
        Route::post('/citas', [CitasController::class, 'store']);
        Route::get('/citas/{id}', [CitasController::class, 'show']);
        Route::put('/citas/{id}', [CitasController::class, 'update']);
        Route::delete('/citas/{id}', [CitasController::class, 'destroy']);
    });

});
