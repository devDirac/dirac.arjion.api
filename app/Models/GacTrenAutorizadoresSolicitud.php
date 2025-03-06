<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GacTrenAutorizadoresSolicitud extends Model
{
    use HasFactory;
    protected $table = 'gac_tren_autorizadores_solicitud';
    public $timestamps = false;
    protected $fillable = [
        'id_usuario',
        'autorizo',
        'requiere_aprobacion',
        'id_solicitud',
        'fecha_registro',
        'comentarios',
        'fecha_visto',
        'fecha_accion'
    ];
}
