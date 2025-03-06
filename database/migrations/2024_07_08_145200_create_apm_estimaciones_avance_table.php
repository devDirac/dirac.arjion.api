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
        Schema::create('apm_estimaciones_avance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_concepto')->nullable();
            $table->foreign('id_concepto')->references('id')->on('apm_conceptos');
            $table->unsignedBigInteger('id_avance')->nullable();
            $table->foreign('id_avance')->references('id')->on('apm_avance');
            $table->unsignedBigInteger('id_estimacion')->nullable();
            $table->foreign('id_estimacion')->references('id')->on('apm_estimaciones_definitivas');
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
        Schema::dropIfExists('apm_estimaciones_avance');
    }
};
