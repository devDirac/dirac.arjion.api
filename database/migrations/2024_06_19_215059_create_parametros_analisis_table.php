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
        Schema::create('parametros_analisis', function (Blueprint $table) {
            $table->id();
            $table->longText('nombre');
            $table->longText('descripcion');
            $table->timestamp('fecha_registro')->default(now());
            $table->text('id_usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parametros_analisis');
    }
};
