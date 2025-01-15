<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmCatIsumos extends Model
{
    use HasFactory;
    protected $table = 'apm_cat_insumos';
    public $timestamps = false;
    protected $fillable = [
        'estatus',
        'prioridad',
        'codigo',
        'concepto',
        'unidad',
        'cantidad',
        'precio',
        'importe',
        'incidencia',
        'reorden',
        'id_obra',
        'id_usuario',
        'fecha_registro',

    ];
}
