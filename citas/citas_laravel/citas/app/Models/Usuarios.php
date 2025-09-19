<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; 
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Notifications\Notifiable;

class Usuarios extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'rol',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relación: un paciente puede tener muchas citas
     */
    public function citasComoPaciente()
    {
        return $this->hasMany(Citas::class, 'id_paciente');
    }

    /**
     * Relación: un médico puede tener muchas citas
     */
    public function citasComoMedico()
    {
        return $this->hasMany(Citas::class, 'id_medico');
    }

    /**
     * Relación: un médico puede tener muchas especialidades
     */
    public function especialidades()
    {
        return $this->belongsToMany(Especialidades::class, 'medico_especialidad', 'id_medico', 'id_especialidad');
    }
}
