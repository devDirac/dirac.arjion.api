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
        Schema::create('apm_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('nombre_corto');
            $table->string('rfc');
            $table->boolean('estatus');
            $table->timestamp('fecha_registro')->nullable();
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->unsignedBigInteger('id_obra');
            $table->foreign('id_obra')->references('id')->on('apm_obras');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_clientes');
    }
};
