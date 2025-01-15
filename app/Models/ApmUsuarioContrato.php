<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmUsuarioContrato extends Model
{
    use HasFactory;
    protected $table = 'apm_usuario_contrato';
    public $timestamps = false;
    protected $fillable = [
        'id_usuario',
        'id_contrato',
        'id_puesto',
        'id_perfil',
        'orden',
        'id_landin_page',
        'permisos_archivos',
        'ultimo_acceso',
        'fecha_registro'
    ];
}