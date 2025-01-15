<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmResponsablesConceptos extends Model
{
    use HasFactory;
    protected $table = 'apm_responsables_conceptos';
    public $timestamps = false;
    protected $fillable = [
        'id_concepto',
        'id_responsable',
        'porcentaje',
        'fecha_registro',
        'id_usuario'
    ];
}
