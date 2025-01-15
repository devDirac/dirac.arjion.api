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
        Schema::create('apm_contratos', function (Blueprint $table) {
            $table->id();
            $table->text('contrato');
            $table->string('id_contrato');
            $table->unsignedBigInteger('id_contratista')->nullable();
            $table->foreign('id_contratista')->references('id')->on('apm_contratistas');
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_final')->nullable();
            $table->decimal('importe');
            $table->integer('estatus');
            $table->unsignedBigInteger('id_cliente');
            $table->foreign('id_cliente')->references('id')->on('apm_clientes');
            $table->unsignedBigInteger('id_responsable')->nullable();
            $table->foreign('id_responsable')->references('id')->on('users');
            $table->integer('autorizado');
            $table->unsignedBigInteger('id_autorizador')->nullable();
            $table->foreign('id_autorizador')->references('id')->on('users');
            $table->integer('plantilla');
            $table->integer('terminado');
            $table->text('nota');
            $table->unsignedBigInteger('id_tipo_contrato')->nullable();
            $table->foreign('id_tipo_contrato')->references('id')->on('apm_cat_tipo_contrato');
            $table->unsignedBigInteger('id_obra_principal')->nullable();
            $table->foreign('id_obra_principal')->references('id')->on('apm_obras');
            $table->unsignedBigInteger('id_tipo_proyecto')->nullable();
            $table->unsignedBigInteger('pep')->nullable();
            $table->foreign('pep')->references('id')->on('apm_pep');
            $table->integer('moneda');
            $table->bigInteger('anticipo');
            $table->text('categoria');
            $table->integer('alertas');
            $table->timestamp('fecha_limite')->nullable();
            $table->integer('reclasificacion');
            $table->integer('propietario');
            $table->timestamp('fecha_registro')->nullable();
            $table->unsignedBigInteger('tipo_contrato_ext')->nullable();
            $table->foreign('tipo_contrato_ext')->references('id')->on('apm_cat_tipo_contrato_ext');
            $table->integer('tolerancia');
            $table->string('estatus_firma');
            $table->string('contrato_liberado');
            $table->unsignedBigInteger('clasificacion_contrato')->nullable();;
            $table->foreign('clasificacion_contrato')->references('id')->on('apm_cat_clasificacion_contrato');
            $table->decimal('tipo_cambio');
            $table->unsignedBigInteger('id_especialidad')->nullable();
            $table->foreign('id_especialidad')->references('id')->on('apm_cat_especialidades');
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
        Schema::dropIfExists('apm_contratos');
    }
};
