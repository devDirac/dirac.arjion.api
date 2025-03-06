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
        Schema::create('apm_contratos_detalle_pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idProyecto');
            $table->foreign('idProyecto')->references('id')->on('apm_obras');
            $table->unsignedBigInteger('idContrato')->nullable();
            $table->foreign('idContrato')->references('id')->on('apm_contratos');
            $table->string('ElementoPEP');
            $table->string('Concepto');
            $table->string('FolioEstimacionLiverpool');
            $table->bigInteger('FolioEstimacionDirac');
            $table->decimal('ImporteFondoGarantia');
            $table->string('idPedido');
            $table->string('idPedidoFondoGarantia');
            $table->decimal('ImporteContratoMD');
            $table->decimal('ImporteContratoML');
            $table->decimal('ImportePedidoMD');
            $table->decimal('ImportePedidoML');
            $table->decimal('ImporteFacturado');
            $table->string('idFactura');
            $table->date('FechaContabilizacion')->nullable();
            $table->date('FechaContabilizacionFondoGarantia')->nullable();
            $table->date('FechaPago')->nullable();
            $table->date('FechaPagoFondoGarantia')->nullable();
            $table->decimal('ImportePagado');
            $table->integer('EsAnticipo');
            $table->decimal('ImporteEstimado');
            $table->decimal('ImporteAmortizado');
            $table->decimal('ImporteDeducido');
            $table->string('idMoneda');
            $table->date('FechaActualizacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_contratos_detalle_pedidos');
    }
};
