<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmUsuariosArchivosCompartidos extends Model
{
    use HasFactory;
    protected $table = 'apm_usuarios_archivos_compartidos';
    public $timestamps = false;
    protected $fillable = [
        'path',
        'estatus',
        'permisos',
        'chm',
        'id_obra',
        'id_usuario',
        'fecha_registro'
    ];
}
