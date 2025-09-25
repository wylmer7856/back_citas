<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\EspecialidadesController;
use App\Http\Controllers\MedicoEspecialidadController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\HistorialController;

// ----------------------------
// RUTAS PÚBLICAS
// ----------------------------
Route::post('/register', [UsuariosController::class, 'register']);
Route::post('/login', [UsuariosController::class, 'login']);

// ----------------------------
// RUTAS PROTEGIDAS
// ----------------------------
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [UsuariosController::class, 'logout']);
    Route::get('/perfil', [UsuariosController::class, 'perfil']);

    // ----------------------------
    // ADMIN
    // ----------------------------
    Route::middleware('role:ADMIN')->group(function () {
        // Usuarios
        Route::get('/usuarios', [UsuariosController::class, 'index']);
        Route::post('/usuarios', [UsuariosController::class, 'store']);
        Route::get('/usuarios/{id}', [UsuariosController::class, 'show']);
        Route::put('/usuarios/{id}', [UsuariosController::class, 'update']);
        Route::delete('/usuarios/{id}', [UsuariosController::class, 'destroy']);

        // Especialidades
        Route::get('/especialidades', [EspecialidadesController::class, 'index']);
        Route::get('/especialidades/{id}', [EspecialidadesController::class, 'show']);
        Route::post('/especialidades', [EspecialidadesController::class, 'store']);
        Route::put('/especialidades/{id}', [EspecialidadesController::class, 'update']);
        Route::delete('/especialidades/{id}', [EspecialidadesController::class, 'destroy']);
        Route::get('/especialidades/buscar', [EspecialidadesController::class, 'buscarPorNombre']);

        // Médico - Especialidad
        Route::get('/medico-especialidad', [MedicoEspecialidadController::class, 'index']);
        Route::get('/medico-especialidad/medico/{id}', [MedicoEspecialidadController::class, 'porMedico']);
        Route::post('/medico-especialidad', [MedicoEspecialidadController::class, 'store']);
        Route::post('/medico-especialidad/multiples', [MedicoEspecialidadController::class, 'asignarMultiples']);
        Route::delete('/medico-especialidad/{id}', [MedicoEspecialidadController::class, 'destroy']);

        // Historiales
        Route::get('/historiales', [HistorialController::class, 'index']);
        Route::get('/historiales/{id}', [HistorialController::class, 'show']);
        Route::get('/historiales/cita/{id_cita}', [HistorialController::class, 'porCita']);
        Route::delete('/historiales/{id}', [HistorialController::class, 'destroy']);

        // Citas
        Route::get('/citas', [CitasController::class, 'index']);
        Route::get('/citas/{id}', [CitasController::class, 'show']);
        Route::put('/citas/{id}', [CitasController::class, 'update']);
        Route::delete('/citas/{id}', [CitasController::class, 'destroy']);
    });

    // ----------------------------
    // MEDICO
    // ----------------------------
    Route::middleware('role:MEDICO')->group(function () {
        // Especialidades
        Route::get('/especialidades', [EspecialidadesController::class, 'index']);
        Route::get('/especialidades/{id}', [EspecialidadesController::class, 'show']);
        Route::get('/especialidades/buscar', [EspecialidadesController::class, 'buscarPorNombre']);

        // Médico - Especialidad
        Route::get('/medico-especialidad', [MedicoEspecialidadController::class, 'index']);
        Route::get('/medico-especialidad/medico/{id}', [MedicoEspecialidadController::class, 'porMedico']);

        // Historiales
        Route::get('/historiales', [HistorialController::class, 'index']);
        Route::get('/historiales/{id}', [HistorialController::class, 'show']);
        Route::get('/historiales/cita/{id_cita}', [HistorialController::class, 'porCita']);
        Route::post('/historiales', [HistorialController::class, 'store']);
        Route::put('/historiales/{id}', [HistorialController::class, 'update']);

        // Citas
        Route::get('/citas', [CitasController::class, 'index']);
        Route::get('/citas/{id}', [CitasController::class, 'show']);
        Route::get('/citas/miscitas', [CitasController::class, 'misCitas']);
    });

    // ----------------------------
    // PACIENTE
    // ----------------------------
    Route::middleware('role:PACIENTE')->group(function () {
        // Especialidades
        Route::get('/especialidades', [EspecialidadesController::class, 'index']);
        Route::get('/especialidades/{id}', [EspecialidadesController::class, 'show']);
        Route::get('/especialidades/buscar', [EspecialidadesController::class, 'buscarPorNombre']);

        // Historiales
        Route::get('/historiales', [HistorialController::class, 'index']);
        Route::get('/historiales/{id}', [HistorialController::class, 'show']);
        Route::get('/historiales/cita/{id_cita}', [HistorialController::class, 'porCita']);

        // Citas
        Route::get('/citas', [CitasController::class, 'index']);
        Route::get('/citas/{id}', [CitasController::class, 'show']);
        Route::get('/citas/miscitas', [CitasController::class, 'misCitas']);
        Route::post('/citas', [CitasController::class, 'store']);
        Route::put('/citas/{id}', [CitasController::class, 'update']);
        Route::delete('/citas/{id}', [CitasController::class, 'destroy']);
    });
    
});
