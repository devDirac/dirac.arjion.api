<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmClientes extends Model
{
    use HasFactory;
    protected $table = 'apm_clientes';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'nombre_corto',
        'rfc',
        'estatus',
        'fecha_registro',
        'id_usuario',
        'id_obra'
    ];
}
