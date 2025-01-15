<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmValoresProyecto extends Model
{
    use HasFactory;
    protected $table = 'apm_valores_proyecto';
    public $timestamps = false;
    protected $fillable = [
        'id_cat_valor',
        'id',
        'id_obra',
        'id_contrato',
        'id_contratista',
        'pedido',
        'importe',
        'estatus',
        'fecha_registro',
        'id_usuario'
    ];
}
