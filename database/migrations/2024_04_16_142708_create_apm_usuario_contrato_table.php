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
        Schema::create('apm_usuario_contrato', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->unsignedBigInteger('id_contrato');
            $table->foreign('id_contrato')->references('id')->on('apm_contratos');
            $table->unsignedBigInteger('id_puesto');
            $table->foreign('id_puesto')->references('id')->on('apm_cat_puestos');
            $table->unsignedBigInteger('id_perfil');
            $table->foreign('id_perfil')->references('id')->on('apm_cat_perfiles_cliente');
            $table->integer('orden')->nullable();
            $table->unsignedBigInteger('id_landin_page');
            $table->foreign('id_landin_page')->references('id')->on('apm_cat_pagina_inicio');
            $table->timestamp('fecha_registro')->nullable();
            $table->string('permisos_archivos')->nullable();
            $table->timestamp('ultimo_acceso')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_usuario_contrato');
    }
};
