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
        Schema::create('apm_actividades_conceptos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_concepto');
            $table->foreign('id_concepto')->references('id')->on('apm_conceptos');
            $table->text('fecha_inicio');
            $table->text('fecha_fin');
            $table->unsignedBigInteger('id_responsable');
            $table->foreign('id_responsable')->references('id')->on('users');
            $table->longText('comentarios');
            $table->dateTime('fecha_captura')->nullable()->default(now());
            $table->dateTime('fecha_reprogramada')->nullable()->default(now());
            $table->boolean('estatus');
            $table->integer('cantidad');
            $table->integer('costo');
            $table->integer('autorizacion');
            $table->integer('dias_anticipacion');
            $table->integer('dias_anticipacion2');
            $table->text('clave');
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
        Schema::dropIfExists('apm_actividades_conceptos');
    }
};
