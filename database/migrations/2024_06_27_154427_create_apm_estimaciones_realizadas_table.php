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
        Schema::create('apm_estimaciones_realizadas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_concepto');
            $table->foreign('id_concepto')->references('id')->on('apm_conceptos');
            $table->decimal('cantidad');
            $table->decimal('importe');
            $table->decimal('volumen_estimar');
            $table->timestamp('fecha_inicio')->nullable();   
            $table->timestamp('fecha_fin')->nullable();   
            $table->timestamp('fecha_registro')->nullable()->default(now());   
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->longText('comentario')->nullable();
            $table->unsignedBigInteger('id_estimacion')->nullable();
            $table->boolean('estatus')->default(true);
            $table->unsignedBigInteger('plaza_arjion')->nullable();
            $table->unsignedBigInteger('tarea_arjion')->nullable();
            $table->longText('avances_ids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_estimaciones_realizadas');
    }
};
