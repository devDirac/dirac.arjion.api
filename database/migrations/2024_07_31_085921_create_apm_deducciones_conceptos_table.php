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
        Schema::create('apm_deducciones_conceptos', function (Blueprint $table) {
            
            $table->id();

            $table->unsignedBigInteger('id_concepto');
            $table->foreign('id_concepto')->references('id')->on('apm_conceptos');

            $table->unsignedBigInteger('id_estimacion_anterior');
            $table->foreign('id_estimacion_anterior')->references('id')->on('apm_estimaciones_definitivas');

            $table->unsignedBigInteger('id_estimacion_actual');
            $table->foreign('id_estimacion_actual')->references('id')->on('apm_estimaciones_definitivas');

            $table->decimal('volumen_deducir',6)->nullable();

            $table->integer('estatus')->nullable()->default(1);

            $table->date('fecha_registro')->nullable()->default(now());
            
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
        Schema::dropIfExists('apm_deducciones_conceptos');
    }
};
