<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmAvanceProgramado extends Model
{
    use HasFactory;
    protected $table = 'apm_avance_programado';
    public $timestamps = false;
    protected $fillable = [
        'fecha',
        'importe',
        'motivo',
        'num_convenio',
        'es_ajustado',
        'id_contrato',
        'id_concepto',
        'id_frente',
        'fecha_actualizacion',
        'fecha_registro',
        'id_usuario',
        'id_programas_guardados'
    ];
}
