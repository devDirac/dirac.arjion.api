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
        Schema::create('apm_insumos_entradas', function (Blueprint $table) {
            $table->id();
            $table->decimal('cantidad',6);
            $table->decimal('precio',6);
            $table->boolean('estatus');
            $table->date('fecha_entrada')->nullable();
            $table->unsignedBigInteger('id_insumo')->nullable();
            $table->foreign('id_insumo')->references('id')->on('apm_cat_insumos');            
            $table->unsignedBigInteger('id_obra')->nullable();
            $table->foreign('id_obra')->references('id')->on('apm_obras');            

            $table->unsignedBigInteger('id_origen')->nullable();
            $table->foreign('id_origen')->references('id')->on('apm_cat_origen_destino'); 

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
        Schema::dropIfExists('apm_insumos_entradas');
    }
};
