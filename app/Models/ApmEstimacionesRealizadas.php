<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmEstimacionesRealizadas extends Model
{
    use HasFactory;
    protected $table = 'apm_estimaciones_realizadas';
    public $timestamps = false;
    protected $fillable = [
        'id_concepto',
        'cantidad',
        'importe',
        'volumen_estimar',
        'fecha_inicio',
        'fecha_fin',
        'fecha_registro',
        'id_usuario',
        'comentario',
        'id_estimacion',
        'estatus',
        'plaza_arjion',
        'tarea_arjion',
        'avances_ids'
    ];
}
