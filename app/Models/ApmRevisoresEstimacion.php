<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmRevisoresEstimacion extends Model
{
    use HasFactory;
    protected $table = 'apm_revisores_estimacion';
    public $timestamps = false;
    protected $fillable = [
        'estatus_estimacion',
        'id_estimacion',
        'id_usuario',
        'fecha_registro'
    ];
}
