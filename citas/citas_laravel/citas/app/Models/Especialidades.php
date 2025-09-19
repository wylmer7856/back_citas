<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidades extends Model
{
    protected $table = 'especialidades';

    protected $fillable = [
        'nombre'
    ];

    /**
     * Relación: una especialidad puede tener muchos médicos
     */
    public function medicos()
    {
        return $this->belongsToMany(Usuarios::class, 'medico_especialidad', 'id_especialidad', 'id_medico');
    }
}
