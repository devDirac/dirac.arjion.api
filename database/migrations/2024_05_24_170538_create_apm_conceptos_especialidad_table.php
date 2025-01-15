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
        Schema::create('apm_conceptos_especialidad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_concepto');
            $table->foreign('id_concepto')->references('id')->on('apm_conceptos');
            $table->unsignedBigInteger('id_subespecialidad');
            $table->foreign('id_subespecialidad')->references('id')->on('apm_cat_especialidades');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_conceptos_especialidad');
    }
};
