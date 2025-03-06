<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmEntradasSalidasRelacion extends Model
{
    use HasFactory;
    protected $table = 'apm_entradas_salidas_relacion';
    public $timestamps = false;
    protected $fillable = [
        'cantidad',
        'precio',
        'id_entrada',
        'id_salida',
        'id_usuario',
        'fecha_registro'
    ];
}
