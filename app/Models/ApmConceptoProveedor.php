<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmConceptoProveedor extends Model
{
    use HasFactory;
    protected $table = 'apm_concepto_proveedor';
    public $timestamps = false;
    protected $fillable = [
        'id_concepto',
        'nombre',
        'direccion',
        'correo',
        'telefono',
        'notas',
        'fecha_registro',
        'id_usuario'
    ];
}
