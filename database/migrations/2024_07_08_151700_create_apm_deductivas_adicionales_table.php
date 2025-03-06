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
        Schema::create('apm_deductivas_adicionales', function (Blueprint $table) {
            $table->id();
            $table->string('folio');
            $table->unsignedBigInteger('id_contratista')->nullable();
            $table->foreign('id_contratista')->references('id')->on('apm_contratistas');
            $table->unsignedBigInteger('id_contrato');
            $table->foreign('id_contrato')->references('id')->on('apm_contratos');
            $table->text('concepto');
            $table->longText('descripcion');
            $table->decimal('monto', total: 20, places: 6);
            $table->unsignedBigInteger('id_estimacion')->nullable();
            $table->foreign('id_estimacion')->references('id')->on('apm_estimaciones_definitivas');
            $table->boolean('aplicado')->default(false);
            $table->timestamp('fecha_registro')->nullable()->default(now()); 
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
        Schema::dropIfExists('apm_deductivas_adicionales');
    }
};
