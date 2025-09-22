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
    Route::middleware('role:ADMIN')->group(function () {
        // Gestión de usuarios
        Route::get('/listarusuarios', [UsuariosController::class, 'index']);
        Route::post('/Crearusuarios', [UsuariosController::class, 'store']);
        Route::get('/buscrausuarios/{id}', [UsuariosController::class, 'show']);
        Route::put('/editarusuarios/{id}', [UsuariosController::class, 'update']);
        Route::delete('/eliminarusuarios/{id}', [UsuariosController::class, 'destroy']);

        // Gestión de especialidades

        Route::get('/listarespecialidades', [EspecialidadesController::class, 'index']);
        Route::post('/crearespecialidades', [EspecialidadesController::class, 'store']);
        Route::put('/editarespecialidades/{id}', [EspecialidadesController::class, 'update']);
        Route::delete('/eliminarespecialidades/{id}', [EspecialidadesController::class, 'destroy']);

        // Asignar especialidades a médicos
        Route::get('/listarmedico-especialidad', [MedicoEspecialidadController::class, 'index']);
        Route::post('/crearmedico-especialidad', [MedicoEspecialidadController::class, 'store']);
        Route::delete('/eliminarmedico-especialidad/{id}', [MedicoEspecialidadController::class, 'destroy']);

        // Eliminar historiales
        Route::get('/listarhistorial', [HistorialController::class, 'index']);
        Route::delete('/historial/{id}', [HistorialController::class, 'destroy']);
    });

    // ----------------------------
    // ADMIN + MEDICO
    // ----------------------------
    Route::middleware('role:ADMIN,MEDICO')->group(function () {
        // Ver especialidades
        Route::get('/listarespecialidades', [EspecialidadesController::class, 'index']);
        Route::get('/buscareespecialidades/{id}', [EspecialidadesController::class, 'show']);

        // Ver relaciones médico-especialidad
        Route::get('/listarmedico-especialidad', [MedicoEspecialidadController::class, 'index']);

        // Manejo de historiales médicos
        Route::get('/listarhistorial', [HistorialController::class, 'index']);
        Route::get('/buscarhistorial/{id}', [HistorialController::class, 'show']);
        Route::post('/crearhistorial', [HistorialController::class, 'store']);
        Route::put('/eliminarhistorial/{id}', [HistorialController::class, 'update']);
    });

    // ----------------------------
    // MEDICO + PACIENTE
    // ----------------------------
    Route::middleware('role:MEDICO,PACIENTE')->group(function () {
        // Manejo de citas
        Route::get('/listarcitas', [CitasController::class, 'index']);
        Route::post('/crearcitas', [CitasController::class, 'store']);
        Route::get('/buscarcitas/{id}', [CitasController::class, 'show']);
        Route::put('/editarcitas/{id}', [CitasController::class, 'update']);
        Route::delete('/eliminarcitas/{id}', [CitasController::class, 'destroy']);
    });

});
