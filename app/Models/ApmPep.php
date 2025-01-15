<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmPep extends Model
{
    use HasFactory;
    protected $table = 'apm_pep';
    public $timestamps = false;
    protected $fillable = [
        'pep',
        'descripcion',
        'cotizacion',
        'presupuesto',
        'fecha',
        'cuenta',
        'orden',
        'imputable',
        'id_obra',
        'id_usuario',
        'id_estatus'
    ];

}
