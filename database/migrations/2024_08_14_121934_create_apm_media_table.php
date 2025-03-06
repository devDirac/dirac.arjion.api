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
        Schema::create('apm_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_concepto')->nullable();
            $table->foreign('id_concepto')->references('id')->on('apm_conceptos');
            $table->unsignedBigInteger('id_frente')->nullable();
            $table->foreign('id_frente')->references('id')->on('apm_frentes');
            $table->string('fecha');
            $table->integer('es_extraordinario')->nullable();
            $table->unsignedBigInteger('id_tipo');
            $table->foreign('id_tipo')->references('id')->on('apm_cat_media');
            $table->string('nombre');
            $table->text('comentarios');
            $table->text('ruta');
            $table->text('nombreCompleto');
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
        Schema::dropIfExists('apm_media');
    }
};
