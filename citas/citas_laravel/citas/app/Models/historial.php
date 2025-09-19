<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    protected $table = 'historial';

    protected $fillable = [
        'id_cita',
        'diagnostico',
        'receta',
        'observaciones'
    ];

    /**
     * Relación: un historial pertenece a una cita
     */
    public function cita()
    {
        return $this->belongsTo(Citas::class, 'id_cita');
    }
}
