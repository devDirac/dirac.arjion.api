<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmRepositorioDocumentos extends Model
{
    use HasFactory;
    protected $table = 'apm_repositorio_documentos';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nombre',
        'descripcion',
        'ruta',
        'fecha_registro',
        'id_usuario'
    ];
}
