<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmUsuariosGeocercas extends Model
{
    use HasFactory;
    protected $table = 'apm_usuarios_geocercas';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'correo',
        'usuario_geocerca',
        'id_obra',
        'id_contrato',
        'id_usuario',
        'fecha_registro'
    ];

}
