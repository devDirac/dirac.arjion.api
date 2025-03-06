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
        Schema::create('apm_insumos_docs_entradas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tamanio');
            $table->string('tipo');
            $table->string('path');
            $table->unsignedBigInteger('id_entrada')->nullable();
            $table->foreign('id_entrada')->references('id')->on('apm_insumos_entradas');
            $table->unsignedBigInteger('id_obra')->nullable();
            $table->foreign('id_obra')->references('id')->on('apm_obras');            
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->timestamp('fecha_registro')->nullable()->default(now());

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_insumos_docs_entradas');
    }
};
