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
        Schema::create('apm_estimaciones_paquete_generado_sap', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idDirac')->nullable();
            $table->foreign('idDirac')->references('id')->on('apm_estimaciones_definitivas');
            $table->unsignedBigInteger('idProyecto')->nullable();
            $table->foreign('idProyecto')->references('id')->on('apm_pep');
            $table->unsignedBigInteger('idContrato')->nullable();
            $table->foreign('idContrato')->references('id')->on('apm_contratos');
            $table->unsignedBigInteger('ElementoPEP')->nullable();
            $table->foreign('ElementoPEP')->references('id')->on('apm_pep');
            $table->string('Concepto');
            $table->decimal('Deductiva', 17, 6);
            $table->decimal('Amortizacion', 17, 6);
            $table->decimal('ImporteEstimado', 17, 6);
            $table->decimal('ImporteAmortizado', 17, 6);
            $table->decimal('ImporteEstimacion', 17, 6);
            $table->decimal('ImporteTotalEstimaciones', 17, 6);
            $table->decimal('ImportePedidos', 17, 6);
            $table->decimal('IVA', 17, 6);
            $table->decimal('ImporteConIVA', 17, 6);
            $table->string('idAutorizadorJefeObra');
            $table->date('FechaHoraAutorizacion')->nullable()->default(now());
            $table->string('idMoneda');
            $table->decimal('ImporteFondoGarantia', 17, 6);
            $table->decimal('ImporteFondoGarantiaAcumulado', 17, 6);
            $table->integer('ConsecutivoPorContrato');
            $table->string('PedidoFondoGarantia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_estimaciones_paquete_generado_sap');
    }
};
