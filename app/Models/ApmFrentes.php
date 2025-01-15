<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmFrentes extends Model
{
    use HasFactory;

    protected $table = 'apm_frentes';
    public $timestamps = false;
    protected $fillable = [
        'frente',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estatus',
        'id_contrato',
        'id_clasificacion',
        'id_especialidad',
        'id_frente',
        'id_usuario',
        'fecha_registro'
    ];
}
