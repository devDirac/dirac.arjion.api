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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('usuario');
            $table->string('email');
            $table->string('password');
            $table->string('password_sinCifrar');
            $table->string('telefono')->nullable();
            $table->longText('foto');
            $table->integer('activo');
            $table->string('empresa')->nullable();
            $table->unsignedBigInteger('id_tipo_usuario')->nullable();
            $table->foreign('id_tipo_usuario')->references('id')->on('apm_tipo_usuarios');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
