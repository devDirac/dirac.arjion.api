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
        Schema::create('apm_ajustes_tema_usuario', function (Blueprint $table) {
            $table->id();
            $table->string('color_menu_lateral')->nullable();
            $table->string('tipo_menu_lateral')->nullable();
            $table->boolean('cabecera_fija')->nullable();
            $table->boolean('menu_lateral_mini')->nullable();
            $table->boolean('tema_claro_oscuro')->nullable();
            $table->string('idioma')->nullable();
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
        Schema::dropIfExists('apm_ajustes_tema_usuario');
    }
};
