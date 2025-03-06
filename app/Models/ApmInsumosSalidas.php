<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmInsumosSalidas extends Model
{
    use HasFactory;
    protected $table = 'apm_insumos_salidas';
    public $timestamps = false;
    protected $fillable = [
        'cantidad',
        'precio',
        'estatus',
        'fecha_salida',
        'id_insumo',
        'id_obra',
        'id_contrato',
        'id_frente',
        'id_concepto',
        'id_usuario',
        'fecha_registro'
    ];
}
