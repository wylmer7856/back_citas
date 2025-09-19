<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Citas extends Model
{
    protected $table = 'citas';

    protected $fillable = [
        'id_paciente',
        'id_medico',
        'fecha',
        'hora',
        'estado'
    ];

    /**
     * Relación: la cita pertenece a un paciente
     */
    public function paciente()
    {
        return $this->belongsTo(Usuarios::class, 'id_paciente');
    }

    /**
     * Relación: la cita pertenece a un médico
     */
    public function medico()
    {
        return $this->belongsTo(Usuarios::class, 'id_medico');
    }

    /**
     * Relación: la cita tiene un historial
     */
    public function historial()
    {
        return $this->hasOne(Historial::class, 'id_cita');
    }
}
