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
        Schema::create('apm_cat_perfiles_cliente', function (Blueprint $table) {
            $table->id();
            $table->string('clave');
            $table->string('nombre');
            $table->string('descripcion');
            $table->unsignedBigInteger('id_estatus');
            $table->foreign('id_estatus')->references('id')->on('apm_estatus_catalogos');
            $table->timestamp('fecha_registro')->nullable();
            $table->unsignedBigInteger('id_tipo');
            $table->foreign('id_tipo')->references('id')->on('apm_tipo_usuarios');
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
        Schema::dropIfExists('apm_cat_perfiles_cliente');
    }
};
