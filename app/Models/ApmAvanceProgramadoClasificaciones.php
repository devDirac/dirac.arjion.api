<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmAvanceProgramadoClasificaciones extends Model
{
    use HasFactory;
    protected $table = 'apm_avance_programado_clasificaciones';
    public $timestamps = false;
    protected $fillable = [
        'fecha',
        'importe',
        'id_clasificacion',
        'fecha_actualizacion',
        'fecha_registro',
        'id_usuario'
    ];
}
