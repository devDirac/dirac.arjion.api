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
        Schema::create('apm_cat_insumos', function (Blueprint $table) {
            $table->id();
            $table->boolean('estatus');
            $table->integer('prioridad');
            $table->string('codigo');
            $table->text('concepto');
            $table->text('unidad');
            $table->decimal('cantidad',6);
            $table->decimal('precio',6);
            $table->decimal('importe',6);
            $table->decimal('incidencia',6);
            $table->decimal('reorden',6);
            $table->unsignedBigInteger('id_obra')->nullable();
            $table->foreign('id_obra')->references('id')->on('apm_obras');            
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
        Schema::dropIfExists('apm_cat_insumos');
    }
};
