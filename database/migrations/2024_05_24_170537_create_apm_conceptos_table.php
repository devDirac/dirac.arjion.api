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
        Schema::create('apm_conceptos', function (Blueprint $table) {
            $table->id();
            $table->string('inciso');
            $table->unsignedBigInteger('id_contrato');
            $table->foreign('id_contrato')->references('id')->on('apm_contratos');
            $table->unsignedBigInteger('id_frente');
            $table->foreign('id_frente')->references('id')->on('apm_frentes');
            $table->string('concepto');
            $table->text('descripcion');
            $table->text('num_convenio');
            $table->string('unidad');
            $table->decimal('cantidad');
            $table->decimal('pu');
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->timestamp('linea_base')->nullable();
            $table->unsignedBigInteger('tipo_concepto');
            $table->foreign('tipo_concepto')->references('id')->on('apm_cat_tipo_concepto');
            $table->boolean('estatus')->default(true);
            $table->boolean('homologado')->default(true);
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->timestamp('fecha_hoy')->nullable()->default(now());
            $table->boolean('cerrado')->default(true);
            $table->boolean('plaza')->default(true);
            $table->boolean('tarea')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_conceptos');
    }
};
