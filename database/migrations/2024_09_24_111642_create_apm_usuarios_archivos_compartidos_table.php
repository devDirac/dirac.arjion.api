<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apm_usuarios_archivos_compartidos', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->boolean('estatus');
            $table->string('permisos');
            $table->string('chm');
            $table->unsignedBigInteger('id_obra');
            $table->foreign('id_obra')->references('id')->on('apm_obras');
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->timestamp('fecha_registro')->default(now());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_usuarios_archivos_compartidos');
    }
};
