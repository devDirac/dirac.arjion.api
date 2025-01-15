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
        Schema::create('apm_avance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_concepto');
            $table->foreign('id_concepto')->references('id')->on('apm_conceptos');
            $table->decimal('cantidad_visual');
            $table->decimal('cantidad_confirmada')->nullable();
            $table->timestamp('fecha_hora');
            $table->timestamp('fecha_hora_c')->nullable();
            $table->unsignedBigInteger('estatus');
            $table->foreign('estatus')->references('id')->on('apm_cat_estatus_avance');
            $table->integer('tipo');
            $table->text('comentarios');
            $table->text('comentarios_c')->nullable();;
            $table->tinyInteger('id_estimacion')->nullable()->default(0);
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
        Schema::dropIfExists('apm_avance');
    }
};
