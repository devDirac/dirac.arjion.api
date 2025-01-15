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
        Schema::create('apm_usuarios_geocercas', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');

            $table->string('correo')->nullable();

            $table->string('usuario_geocerca');


            $table->unsignedBigInteger('id_obra')->nullable();
            $table->foreign('id_obra')->references('id')->on('apm_obras');
            
            $table->unsignedBigInteger('id_contrato')->nullable();
            $table->foreign('id_contrato')->references('id')->on('apm_contratos');
            
            
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
        Schema::dropIfExists('apm_usuarios_geocercas');
    }
};
