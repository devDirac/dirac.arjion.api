<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GacSolicitud extends Model
{
    use HasFactory;
    protected $table = 'gac_solicitud';
    public $timestamps = false;
    protected $fillable = [
        'solicita',
        'beneficiario',
        'id_proyecto',
        'id_moneda',
        'importe',
        'importe_pesos', 
        'descripcion',
        'id_tipo_solicitud',
        'id_forma_pago',
        'banco',
        'cuenta',
        'clabe',
        'fecha_pago',
        'fecha_solicitud',
        'id_estatus',
        'proyecto_sr',
        'id_empresa',
        'proveedor',
        'id_concepto',
        'id_usuario_revisor',
        'fecha_id_usuario_revisor',
        'autorizo_usuario_revisor',
        'comentarios_usuario_revisor',
        'id_usuario_autorizador',
        'fecha_id_usuario_autorizador',
        'autorizo_usuario_autorizador',
        'comentarios_usuario_autorizador',
        'id_usuario_pagada',
        'fecha_id_usuario_pagada',
        'autorizo_usuario_pagada',
        'comentarios_usuario_pagada',
        'quincenas_numero',
        'quincenas_valor'
    ];
}
