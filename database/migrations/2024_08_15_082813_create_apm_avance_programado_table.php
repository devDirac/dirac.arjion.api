<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apm_avance_programado', function (Blueprint $table) {
            $table->id();
            $table->string('fecha');
            $table->decimal('importe', 60);
            $table->text('motivo');
            $table->integer('num_convenio');
            $table->integer('es_ajustado')->default(0);
            $table->unsignedBigInteger('id_contrato');
            $table->foreign('id_contrato')->references('id')->on('apm_contratos');
            $table->unsignedBigInteger('id_concepto')->nullable();
            $table->foreign('id_concepto')->references('id')->on('apm_conceptos');
            $table->unsignedBigInteger('id_frente')->nullable();
            $table->foreign('id_frente')->references('id')->on('apm_frentes');
            $table->timestamp('fecha_actualizacion')->nullable();
            $table->timestamp('fecha_registro')->nullable()->default(now());
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->unsignedBigInteger('id_programas_guardados');
            $table->foreign('id_programas_guardados')->references('id')->on('apm_programas_financieros_guardados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_avance_programado');
    }
};
