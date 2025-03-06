<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmDeductivasAdicionales extends Model
{
    use HasFactory;
    protected $table = 'apm_deductivas_adicionales';
    public $timestamps = false;
    protected $fillable = [
        'folio',
        'id_contratista',
        'id_contrato',
        'concepto',
        'descripcion',
        'monto',
        'id_estimacion',
        'aplicado',
        'fecha_registro',
        'id_usuario'
    ];
}