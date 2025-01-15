<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmModDocsCalidad extends Model
{
    use HasFactory;

    protected $table = 'apm_mod_docs_calidad';
    public $timestamps = false;
    protected $fillable = [
        'id_contrato',
        'id_especialidad',
        'id_documento',
        'nombre',
        'path',
        'fecha_registro',
        'id_usuario',
        'id_estatus',
        'comentarios',
        'tamanio',
        'comentarios_c' 
    ];
}
