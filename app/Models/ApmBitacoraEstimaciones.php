<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmBitacoraEstimaciones extends Model
{
    use HasFactory;
    protected $table = 'apm_bitacora_estimaciones';
    public $timestamps = false;
    protected $fillable = [
        'id_estimacion',
        'estatus',
        'descripcion',
        'fecha_registro',
        'id_usuario'
    ];
}
