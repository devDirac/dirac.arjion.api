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
        Schema::create('apm_responsables_conceptos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_concepto');
            $table->foreign('id_concepto')->references('id')->on('apm_conceptos');
            $table->unsignedBigInteger('id_responsable');
            $table->foreign('id_responsable')->references('id')->on('users');
            $table->decimal('porcentaje',6);
            $table->timestamp('fecha_registro')->nullable()->default(now());
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
        Schema::dropIfExists('apm_responsables_conceptos');
    }
};
