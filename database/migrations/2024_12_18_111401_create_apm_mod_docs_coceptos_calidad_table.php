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
        Schema::create('apm_mod_docs_coceptos_calidad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_concepto');
            $table->foreign('id_concepto')->references('id')->on('apm_conceptos');
            $table->unsignedBigInteger('id_especialidad');
            $table->foreign('id_especialidad')->references('id')->on('apm_cat_especialidades');
            $table->unsignedBigInteger('id_documento');
            $table->foreign('id_documento')->references('id')->on('apm_cat_especialidades_documentos');
            $table->string('nombre');
            $table->string('nombre_original');
            $table->string('path');
            $table->timestamp('fecha_registro')->nullable()->default(now());
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->unsignedBigInteger('id_estatus');
            $table->foreign('id_estatus')->references('id')->on('apm_mod_estatus_docs_calidad');
            $table->longText('comentarios');
            $table->string('tamanio');
            $table->longText('comentarios_c');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_mod_docs_coceptos_calidad');
    }
};
