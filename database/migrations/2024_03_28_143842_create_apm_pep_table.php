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
        Schema::create('apm_pep', function (Blueprint $table) {
            $table->id();
            $table->string('pep');
            $table->string('descripcion');
            $table->string('cotizacion');
            $table->string('presupuesto');
            $table->timestamp('fecha')->nullable();
            $table->string('cuenta');
            $table->string('orden');
            $table->string('imputable');
            $table->unsignedBigInteger('id_obra')->nullable();
            $table->foreign('id_obra')->references('id')->on('apm_obras');
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->unsignedBigInteger('id_estatus');
            $table->foreign('id_estatus')->references('id')->on('apm_estatus_catalogos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_pep');
    }
};
