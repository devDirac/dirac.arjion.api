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
        Schema::create('apm_frentes', function (Blueprint $table) {
            $table->id();
            $table->string('frente');
            $table->text('descripcion');
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->boolean('estatus')->default(true);
            $table->unsignedBigInteger('id_contrato');
            $table->foreign('id_contrato')->references('id')->on('apm_contratos');
            $table->unsignedBigInteger('id_clasificacion')->nullable();
            $table->foreign('id_clasificacion')->references('id')->on('apm_cat_clasificacion_frentes');
            $table->unsignedBigInteger('id_especialidad')->nullable();
            $table->foreign('id_especialidad')->references('id')->on('apm_cat_especialidades');
            $table->unsignedBigInteger('id_frente')->nullable()->default(0);
            $table->unsignedBigInteger('id_usuario');
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
        Schema::dropIfExists('apm_frentes');
    }
};
