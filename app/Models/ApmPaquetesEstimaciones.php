<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmPaquetesEstimaciones extends Model
{
    use HasFactory;
    protected $table = 'apm_paquetes_estimaciones';
    public $timestamps = false;
    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'id_estimacion',
        'id_usuario',
        'fecha_registro'
    ];
}
