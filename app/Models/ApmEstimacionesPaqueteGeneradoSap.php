<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmEstimacionesPaqueteGeneradoSap extends Model
{
    use HasFactory;
    protected $table = 'apm_estimaciones_paquete_generado_sap';
    public $timestamps = false;
    protected $fillable = [
        'idDirac',
        'idProyecto',
        'idContrato',
        'ElementoPEP',
        'Concepto',
        'Deductiva',
        'Amortizacion',
        'ImporteEstimado',
        'ImporteAmortizado',
        'ImporteEstimacion',
        'ImporteTotalEstimaciones',
        'ImportePedidos',
        'IVA',
        'ImporteConIVA',
        'idAutorizadorJefeObra',
        'FechaHoraAutorizacion',
        'idMoneda',
        'ImporteFondoGarantia',
        'ImporteFondoGarantiaAcumulado',
        'ConsecutivoPorContrato',
        'PedidoFondoGarantia'
    ];
}
