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
        Schema::create('apm_pep_contrato', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pep');
            $table->foreign('id_pep')->references('id')->on('apm_pep');
            $table->unsignedBigInteger('id_contrato');
            $table->foreign('id_contrato')->references('id')->on('apm_contratos');
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
        Schema::dropIfExists('apm_pep_contrato');
    }
};
