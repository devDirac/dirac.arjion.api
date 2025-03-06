<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmModDocsCoceptosCalidad extends Model
{
    use HasFactory;
    protected $table = 'apm_mod_docs_coceptos_calidad';
    public $timestamps = false;
    protected $fillable = [
        'id_concepto',
        'id_especialidad',
        'id_documento',
        'nombre',
        'nombre_original',
        'path',
        'fecha_registro',
        'id_usuario',
        'id_estatus',
        'comentarios',
        'tamanio',
        'comentarios_c'
    ];
}
