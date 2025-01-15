<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmNotificacionesUsuario extends Model
{
    use HasFactory;
    protected $table = 'apm_notificaciones_usuario';
    public $timestamps = false;
    protected $fillable = [
        'detalle',
        'creada',
        'vista_fecha',
        'vista',
        'id_tipo_notificacion',
        'id_usuario_creador',
        'id_usuario_para',
        'id_contrato',
        'id_obra'
    ];
}
