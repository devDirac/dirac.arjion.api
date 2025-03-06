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
        Schema::create('apm_obras', function (Blueprint $table) {
            $table->id();
            $table->string('obra');
            $table->string('descripcion');
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->timestamp('fecha_registro')->nullable();
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->unsignedBigInteger('id_estatus');
            $table->foreign('id_estatus')->references('id')->on('apm_estatus_catalogos');
            $table->string('latitud');
            $table->string('longitud');
            $table->text('address');
            $table->text('presupuesto');
            $table->string('limite_contratos');
            $table->longText('foto');
            $table->unsignedBigInteger('id_anterior');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_obras');
    }
};
