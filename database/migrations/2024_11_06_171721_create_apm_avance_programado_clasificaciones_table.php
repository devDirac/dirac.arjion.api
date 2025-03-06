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
        Schema::create('apm_avance_programado_clasificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('fecha');
            $table->decimal('importe', 60);
            $table->unsignedBigInteger('id_clasificacion');
            $table->foreign('id_clasificacion')->references('id')->on('apm_cat_clasificacion_contrato');
            $table->timestamp('fecha_actualizacion')->nullable();
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
        Schema::dropIfExists('apm_avance_programado_clasificaciones');
    }
};
