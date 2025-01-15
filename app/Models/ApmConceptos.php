<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmConceptos extends Model
{
    use HasFactory;
    protected $table = 'apm_conceptos';
    public $timestamps = false;
    protected $fillable = [
        'inciso',
        'id_contrato',
        'id_frente',
        'concepto',
        'descripcion',
        'num_convenio',
        'unidad',
        'cantidad',
        'pu',
        'fecha_inicio',
        'fecha_fin',
        'linea_base',
        'tipo_concepto',
        'estatus',
        'homologado',
        'id_usuario',
        'fecha_hoy',
        'cerrado',
        'plaza',
        'tarea',
        'id_concepto_subespecialidad'
    ];
}
