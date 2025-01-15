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
        Schema::create('apm_entradas_salidas_relacion', function (Blueprint $table) {
            $table->id();
            $table->decimal('cantidad', 17, 6);
            $table->decimal('precio', 17, 6);
            $table->unsignedBigInteger('id_entrada')->nullable();
            $table->foreign('id_entrada')->references('id')->on('apm_insumos_entradas');
            $table->unsignedBigInteger('id_salida')->nullable();
            $table->foreign('id_salida')->references('id')->on('apm_insumos_salidas');
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
        Schema::dropIfExists('apm_entradas_salidas_relacion');
    }
};
