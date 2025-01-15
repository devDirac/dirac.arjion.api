<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmMedia extends Model
{
    use HasFactory;
    protected $table = 'apm_media';
    public $timestamps = false;
    protected $fillable = [
        'id_concepto',
        'id_frente',
        'fecha',
        'id_tipo',
        'nombre',
        'es_extraordinario',
        'ruta',
        'nombreCompleto',
        'comentarios',
        'fecha_registro',
        'id_usuario',
        'embedding_base64'
    ];
}
