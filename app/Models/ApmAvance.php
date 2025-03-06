<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmAvance extends Model
{
    use HasFactory;
    protected $table = 'apm_avance';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'id_concepto',
        'cantidad_visual',
        'cantidad_confirmada',
        'fecha_hora',
        'fecha_hora_c',
        'estatus',
        'tipo',
        'comentarios',
        'comentarios_c',
        'id_estimacion',
        'fecha_registro',
        'id_usuario'
    ];
}
