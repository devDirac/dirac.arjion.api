<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmEstimacionesFondoGarantia extends Model
{
    use HasFactory;
    protected $table = 'apm_estimaciones_fondo_garantia';
    public $timestamps = false;
    protected $fillable = [
        'id_estimacion',
        'numero_pedido',
        'fecha_pedido',
        'fecha_pago_lvpl',
        'contabilizado',
        'pagado',
        'pendiente_contabilizar',
        'fecha_actualizacion'
    ];
}
