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
        Schema::create('apm_equipo_maquinaria', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('unidad');
            $table->string('precio');
            $table->unsignedBigInteger('id_obra')->nullable();
            $table->foreign('id_obra')->references('id')->on('apm_obras');
            $table->unsignedBigInteger('id_tipo');
            $table->foreign('id_tipo')->references('id')->on('apm_cat_equipo_maquinaria');
            $table->unsignedBigInteger('id_estatus');
            $table->foreign('id_estatus')->references('id')->on('apm_estatus_catalogos');
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->timestamp('fecha_registro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_equipo_maquinaria');
    }
};
