<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmObras extends Model
{
    
    use HasFactory;
    protected $table = 'apm_obras';
    public $timestamps = false;
    protected $fillable = [
        'obra',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'fecha_registro',
        'id_usuario',
        'id_estatus',
        'latitud',
        'longitud',
        'address',
        'presupuesto',
        'limite_contratos',
        'foto'
    ];
}
