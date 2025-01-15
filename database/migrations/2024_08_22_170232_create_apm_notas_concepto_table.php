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
        Schema::create('apm_notas_concepto', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->longText('nota');
            $table->unsignedBigInteger('id_tipo_nota');
            $table->foreign('id_tipo_nota')->references('id')->on('apm_cat_tipo_nota');
            $table->unsignedBigInteger('id_concepto');
            $table->foreign('id_concepto')->references('id')->on('apm_conceptos');
            $table->timestamp('fecha_registro')->nullable();
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
        Schema::dropIfExists('apm_notas_concepto');
    }
};
