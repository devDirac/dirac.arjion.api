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
        Schema::create('apm_mod_docs_bitacora_c', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_documento');
            $table->foreign('id_documento')->references('id')->on('apm_cat_especialidades_documentos');
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->unsignedBigInteger('id_estatus');
            $table->foreign('id_estatus')->references('id')->on('apm_mod_estatus_docs_bitacora');
            $table->longText('descripcion');
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
        Schema::dropIfExists('apm_mod_docs_bitacora_c');
    }
};
