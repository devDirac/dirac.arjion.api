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
        Schema::create('gac_cat_tipo_solicitud', function (Blueprint $table) {
            $table->id();
            $table->string('clave');
            $table->string('nombre');
            $table->string('descripcion');
            $table->integer('requiere_beneficiario')->default(1);
            $table->integer('requiere_documentos')->default(1);
            $table->integer('requiere_concepto')->default(1);
            $table->integer('mostrar_pago_quincenas')->default(0);
            $table->unsignedBigInteger('estatus');
            $table->timestamp('fecha_registro')->nullable();
            $table->unsignedBigInteger('id_usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gac_cat_tipo_solicitud');
    }
};
