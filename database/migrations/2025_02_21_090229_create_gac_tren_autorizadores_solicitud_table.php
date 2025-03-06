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
        Schema::create('gac_tren_autorizadores_solicitud', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->boolean('autorizo')->nullable();
            $table->boolean('requiere_aprobacion')->default(true);
            $table->unsignedBigInteger('id_solicitud');
            $table->foreign('id_solicitud')->references('id')->on('gac_solicitud');
            $table->timestamp('fecha_registro')->nullable();
            $table->text('comentarios');
            $table->timestamp('fecha_visto')->nullable();
            $table->timestamp('fecha_accion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gac_tren_autorizadores_solicitud');
    }
};
