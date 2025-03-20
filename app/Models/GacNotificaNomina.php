<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GacNotificaNomina extends Model
{
    use HasFactory;
    protected $table = 'gac_notifica_nomina';
    public $timestamps = false;
    protected $fillable = [
        'id_solicitud',
        'importe',
        'fecha_registro',
        'id_usuario_notifica',
        'id_usuario_recibe_notificacion',
    ];
}
