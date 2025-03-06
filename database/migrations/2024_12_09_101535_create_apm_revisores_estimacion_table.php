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
        Schema::create('apm_revisores_estimacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estatus_estimacion')->nullable();
            $table->unsignedBigInteger('id_estimacion')->nullable();
            $table->foreign('id_estimacion')->references('id')->on('apm_estimaciones_definitivas');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->foreign('id_usuario')->references('id')->on('users');
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
        Schema::dropIfExists('apm_revisores_estimacion');
    }
};
