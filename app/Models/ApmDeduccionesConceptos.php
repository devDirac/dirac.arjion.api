<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmDeduccionesConceptos extends Model
{
    use HasFactory;
    protected $table = 'apm_deducciones_conceptos';
    public $timestamps = false;
    protected $fillable = [
        'id_concepto',
        'id_estimacion_anterior',
        'id_estimacion_actual',
        'volumen_deducir',
        'estatus',
        'fecha_registro',
        'id_usuario'
    ];
}
