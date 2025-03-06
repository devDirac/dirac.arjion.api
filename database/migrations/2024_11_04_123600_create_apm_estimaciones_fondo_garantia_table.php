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
        Schema::create('apm_estimaciones_fondo_garantia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_estimacion');
            $table->foreign('id_estimacion')->references('id')->on('apm_estimaciones_definitivas');
            $table->string('numero_pedido')->nullable();
            $table->date('fecha_pedido')->nullable();
            $table->timestamp('fecha_pago_lvpl')->nullable();   
            $table->integer('contabilizado')->nullable();
            $table->integer('pagado')->nullable();
            $table->integer('pendiente_contabilizar')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_estimaciones_fondo_garantia');
    }
};
