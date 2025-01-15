<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmInsumosEntradas extends Model
{
    use HasFactory;
    protected $table = 'apm_insumos_entradas';
    public $timestamps = false;
    protected $fillable = [
        'cantidad',
        'precio',
        'estatus',
        'fecha_entrada',
        'id_insumo',
        'id_obra',
        'id_origen',
        'id_usuario',
        'fecha_registro'
    ];
}
