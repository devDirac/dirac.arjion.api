<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GacPerfilSolicitud extends Model
{
    use HasFactory;
    protected $table = 'gac_perfil_solicitud';
    public $timestamps = false;
    protected $fillable = [
        'id_usuario',
        'id_perfil',
        'fecha_registro'
    ];
}
