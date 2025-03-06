<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmEstimacionesAvance extends Model
{
    use HasFactory;
    protected $table = 'apm_estimaciones_avance';
    public $timestamps = false;
    protected $fillable = [
        'id_concepto',
        'id_avance',
        'id_estimacion',
        'fecha_registro',
        'id_usuario'
    ];
}
