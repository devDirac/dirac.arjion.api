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
        Schema::create('apm_reportes_voz', function (Blueprint $table) {
            $table->id();
            $table->longText('texto_original')->nullable();
            $table->longText('resumen');
            $table->string('titulo')->nullable();
            $table->string('ruta')->nullable();
            $table->longText('ids_resumen_generado')->nullable();
            $table->timestamp('fecha_inicio_reporte_generado')->nullable();
            $table->timestamp('fecha_fin_reporte_generado')->nullable();
            $table->text('tipo')->nullable();
            $table->longText('img')->nullable();
            $table->timestamp('fecha_registro')->nullable()->default(now());
            $table->text('id_usuario');
            $table->integer('id_proyecto')->nullable();
            $table->integer('id_contrato')->nullable();
            $table->integer('id_frente')->nullable();
            $table->integer('id_concepto')->nullable();
            $table->integer('id_avance')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_reportes_voz');
    }
};
