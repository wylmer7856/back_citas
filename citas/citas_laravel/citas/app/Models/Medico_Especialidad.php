<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico_Especialidad extends Model
{
    protected $table = 'medico_especialidad';

    protected $fillable = [
        'id_medico',
        'id_especialidad'
    ];

    /**
     * Relación hacia médico
     */
    public function medico()
    {
        return $this->belongsTo(Usuarios::class, 'id_medico');
    }

    /**
     * Relación hacia especialidad
     */
    public function especialidad()
    {
        return $this->belongsTo(Especialidades::class, 'id_especialidad');
    }
}
