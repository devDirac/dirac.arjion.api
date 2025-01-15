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
        Schema::create('apm_contratistas', function (Blueprint $table) {
            $table->id();
            $table->string('contratista');
            $table->string('correo_contratista');
            $table->string('descripcion');
            $table->string('rfc');
            $table->string('id_externo');
            $table->unsignedBigInteger('id_usuario');
            $table->timestamp('fecha_registro')->nullable();
            $table->unsignedBigInteger('id_obra')->nullable();
            $table->unsignedBigInteger('id_estatus');
            $table->string('estatus_bloqueo');
            $table->string('extranjero');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->foreign('id_obra')->references('id')->on('apm_obras');
            $table->foreign('id_estatus')->references('id')->on('apm_estatus_catalogos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_contratistas');
    }
};
