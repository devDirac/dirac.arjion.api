<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmNotificaciones extends Model
{
    use HasFactory;
    protected $table = 'apm_notificaciones';
    public $timestamps = false;
    protected $fillable = [
        'tarea_id',
        'reenviar',
        'horario',
        'correo',
        'telefono_whats',
        'notificacion_en_sistema',
        'id_usuario',
        'id_obra',
        'id_contrato',
        'fecha_registro'
    ];
}
