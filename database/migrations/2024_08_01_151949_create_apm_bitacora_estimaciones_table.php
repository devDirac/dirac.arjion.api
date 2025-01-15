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
        Schema::create('apm_bitacora_estimaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_estimacion');
            $table->foreign('id_estimacion')->references('id')->on('apm_estimaciones_definitivas');
            $table->integer('estatus')->nullable();
            $table->text('descripcion')->nullable();
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
        Schema::dropIfExists('apm_bitacora_estimaciones');
    }
};
