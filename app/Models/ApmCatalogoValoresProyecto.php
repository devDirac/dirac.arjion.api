<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmCatalogoValoresProyecto extends Model
{
    use HasFactory;

    protected $table = 'apm_catalogo_valores_proyecto';
    public $timestamps = false;
    protected $fillable = [
        'id_valor_proyecto',
        'concepto',
        'descripcion',
        'unidad',
        'cantidad',
        'pu',
        'fecha_inicio',
        'fecha_fin',
        'estatus',
        'homologado',
        'id_usuario',
        'fecha_registro'
    ];
}
