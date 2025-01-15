<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apm_estimaciones_definitivas', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_estimacion');
            $table->text('fecha_est_definitiva');
            $table->text('numero_ramo');
            $table->text('iva_aplicado_editado');
            $table->unsignedBigInteger('id_tipo_contrato')->nullable();
            $table->foreign('id_tipo_contrato')->references('id')->on('apm_cat_tipo_contrato');
            $table->text('fecha_contrato');
            $table->text('modalidad_adjudicacion');
            $table->text('est_prov');
            $table->text('fecha_inicio');
            $table->text('fecha_fin');
            $table->text('importe');
            $table->text('amortizacion');
            $table->text('subtotal');
            $table->text('impuesto_iva');
            $table->text('importe_total');
            $table->text('devolucion_retencion');
            $table->text('retencion');
            $table->text('deducciones');
            $table->text('saldo_obra_ejecutada');
            $table->text('beneficio_social');
            $table->text('imdt');
            $table->text('impuesto_srenta');
            $table->text('inspeccion_obras');
            $table->text('otros');
            $table->longText('descripcion_otros')->nullable();
            $table->text('total_deducciones');
            $table->text('alcance_liquido');
            $table->integer('estatus');
            $table->unsignedBigInteger('id_estimacion_padre')->nullable();
            $table->longText('comentarios')->nullable();
            $table->integer('fg_aplicado');
            $table->integer('envio');
            $table->integer('preestimacion');
            $table->integer('factura');
            $table->integer('id_reporte_p');
            $table->integer('paquete_generado');
            $table->string('numero_pedido');
            $table->date('fecha_pedido');
            $table->date('fecha_contabilizado');
            $table->date('fecha_pago')->nullable();
            $table->boolean('pronto_pago')->default(false);
            $table->integer('contabilizado');
            $table->integer('pagado');
            $table->integer('pendiente_contabilizar');
            $table->dateTime('fecha_actualizacion');
            $table->integer('realizada');
            $table->string('importe_tipo_cambio');
            $table->decimal('importePedidoMD', total: 20, places: 6);
            $table->decimal('importePedidoML', total: 20, places: 6);
            $table->integer('revision');
            $table->integer('revisores');
            $table->integer('envio_coordinacion');
            $table->integer('envio_revision');
            $table->integer('p360');
            $table->integer('id_pep');
            $table->timestamp('fecha_registro')->nullable()->default(now());   
            $table->unsignedBigInteger('id_contrato');
            $table->foreign('id_contrato')->references('id')->on('apm_contratos');
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_estimaciones_definitivas');
    }
};
