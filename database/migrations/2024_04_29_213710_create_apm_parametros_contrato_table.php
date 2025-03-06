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
        Schema::create('apm_parametros_contrato', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_contrato');
            $table->foreign('id_contrato')->references('id')->on('apm_contratos');
            $table->text('area_expide');
            $table->text('tipo_contrato');
            $table->timestamp('fecha_contrato')->nullable()->default(now());
            $table->string('modalidad');
            $table->string('clave_presupuestaria');
            $table->string('oficina_pagadora');
            $table->decimal('numero');
            $table->decimal('amortizacion');
            $table->timestamp('fecha')->nullable()->default(now());
            $table->boolean('estatus')->default(true);
            $table->decimal('asignacion_iva');
            $table->string('numero_pedido')->nullable();
            $table->timestamp('fecha_pedido')->nullable()->default(now());
            $table->timestamp('fecha_pago_lvpl')->nullable()->default(now());
            $table->boolean('contabilizado')->nullable()->default(true);
            $table->boolean('pagado')->nullable()->default(true);
            $table->boolean('pendiente_contabilizar')->nullable()->default(true);
            $table->decimal('tipo_cambio')->nullable();
            $table->decimal('fondo_gtia')->nullable();
            $table->timestamp('fecha_contabilizado')->nullable()->default(now());
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
        Schema::dropIfExists('apm_parametros_contrato');
    }
};
