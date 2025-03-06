<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmParametrosContrato extends Model
{
    use HasFactory;
    protected $table = 'apm_parametros_contrato';
    public $timestamps = false;
    protected $fillable = [
        'id_contrato',
        'area_expide',
        'tipo_contrato',
        'fecha_contrato',
        'modalidad',
        'clave_presupuestaria',
        'oficina_pagadora',
        'numero',
        'amortizacion',
        'fecha',
        'estatus',
        'asignacion_iva',
        'numero_pedido',
        'fecha_pedido',
        'fecha_pago_lvpl',
        'contabilizado',
        'pagado',
        'pendiente_contabilizar',
        'tipo_cambio',
        'fondo_gtia',
        'id_usuario',
        'fecha_contabilizado'
    ];
}
