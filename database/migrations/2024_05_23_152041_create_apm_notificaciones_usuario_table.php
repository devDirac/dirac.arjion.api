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
        Schema::create('apm_notificaciones_usuario', function (Blueprint $table) {
            $table->id();
            $table->text('detalle');
            $table->timestamp('creada')->nullable()->default(now());
            $table->timestamp('vista_fecha')->nullable();
            $table->boolean('vista')->nullable();
            $table->unsignedBigInteger('id_tipo_notificacion');
            $table->foreign('id_tipo_notificacion')->references('id')->on('apm_cat_tipo_notificacion');
            $table->unsignedBigInteger('id_usuario_creador');
            $table->foreign('id_usuario_creador')->references('id')->on('users');
            $table->unsignedBigInteger('id_usuario_para');
            $table->foreign('id_usuario_para')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_notificaciones_usuario');
    }
};
