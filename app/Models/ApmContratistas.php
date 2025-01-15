<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmContratistas extends Model
{
    use HasFactory;
    protected $table = 'apm_contratistas';
    public $timestamps = false;
    protected $fillable = [
        'contratista',
        'correo_contratista',
        'descripcion',
        'rfc',
        'id_externo',
        'id_usuario',
        'fecha_registro',
        'id_obra',
        'id_estatus',
        'estatus_bloqueo',
        'extranjero'

    ];
}
