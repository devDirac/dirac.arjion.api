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
        Schema::create('apm_docs_estimaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('path');
            $table->longText('descripcion');
            $table->string('tipo_documento')->nullable();
            $table->unsignedBigInteger('id_estimacion')->nullable();
            $table->foreign('id_estimacion')->references('id')->on('apm_estimaciones_definitivas');
            $table->timestamp('fecha_registro')->nullable()->default(now());
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->boolean('estatus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_docs_estimaciones');
    }
};
