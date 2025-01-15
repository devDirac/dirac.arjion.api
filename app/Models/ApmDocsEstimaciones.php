<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmDocsEstimaciones extends Model
{
    use HasFactory;
    protected $table = 'apm_docs_estimaciones';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'path',
        'descripcion',
        'id_tipo_documento',
        'id_estimacion',
        'fecha_registro',
        'id_usuario',
        'estatus'
    ];
}
