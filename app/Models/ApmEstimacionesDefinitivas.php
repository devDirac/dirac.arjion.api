<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmEstimacionesDefinitivas extends Model
{
    use HasFactory;
    protected $table = 'apm_estimaciones_definitivas';
    public $timestamps = false;
    protected $fillable = [
        'numero_estimacion',
        'fecha_est_definitiva',
        'numero_ramo',
        'iva_aplicado_editado',
        'id_tipo_contrato',
        'fecha_contrato',
        'modalidad_adjudicacion',
        'est_prov',
        'fecha_inicio',
        'fecha_fin',
        'importe',
        'amortizacion',
        'subtotal',
        'impuesto_iva',
        'importe_total',
        'devolucion_retencion',
        'retencion',
        'deducciones',
        'saldo_obra_ejecutada',
        'beneficio_social',
        'imdt',
        'impuesto_srenta',
        'inspeccion_obras',
        'otros',
        'descripcion_otros',
        'total_deducciones',
        'alcance_liquido',
        'estatus',
        'id_estimacion_padre',
        'comentarios',
        'fg_aplicado',
        'envio',
        'preestimacion',
        'factura',
        'id_reporte_p',
        'paquete_generado',
        'numero_pedido',
        'fecha_pedido',
        'fecha_contabilizado',
        'fecha_pago',
        'pronto_pago',
        'contabilizado',
        'pagado',
        'pendiente_contabilizar',
        'fecha_actualizacion',
        'realizada',
        'importe_tipo_cambio',
        'importePedidoMD',
        'importePedidoML',
        'revision',
        'revisores',
        'envio_coordinacion',
        'envio_revision',
        'p360',
        'fecha_registro',
        'id_contrato',
        'id_usuario',
        'id_pep'
    ];
}
