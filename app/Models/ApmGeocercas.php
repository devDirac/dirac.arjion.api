<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmGeocercas extends Model
{
    use HasFactory;

    protected $table = 'apm_geocercas';
    public $timestamps = false;
    protected $fillable = [
        'coordenadas',
        'area',
        'id_obra',
        'id_contrato',
        'id_usuario',
        'fecha_registro'
    ];
}
