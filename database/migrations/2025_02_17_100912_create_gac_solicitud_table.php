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
        Schema::create('gac_solicitud', function (Blueprint $table) {
            $table->id();
            
            $table->integer('solicita');//id del solicitante, viene de una tabla de usuario de arjion

            $table->integer('beneficiario'); // id del beneficiario, es 0 si el beneficiario es externo , viene de una tabla de usuarios de arjion

            $table->integer('id_proyecto'); // id tabla de una arjion (No se cual es)

            $table->unsignedBigInteger('id_moneda'); // id gac_equivalencia_moneda_ext_dol falta 
            $table->foreign('id_moneda')->references('id')->on('gac_equivalencia_moneda_ext_dol');

            $table->decimal('importe', 17, 6); // importe equivalente a la moneda seleccionada

            $table->decimal('importe_pesos', 17, 6); // la conversión con respecto a la tabla gac_tipo_cambio_dolar 

            $table->longText('descripcion'); // campo abierto 
            
            $table->unsignedBigInteger('id_tipo_solicitud');
            $table->foreign('id_tipo_solicitud')->references('id')->on('gac_cat_tipo_solicitud');

            $table->unsignedBigInteger('id_forma_pago'); // id  gac_cat_forma_pago de pago falta la tabla  // cheque y trasnferencia 
            $table->foreign('id_forma_pago')->references('id')->on('gac_cat_forma_pago');

            $table->string('banco'); // campo abierto 
            $table->string('cuenta'); // campo abierto 
            $table->string('clabe'); // campo abierto 

            $table->timestamp('fecha_solicitud');  // fecha default 

            $table->unsignedBigInteger('id_estatus');  // id gac_cat_estatus_solicitud falta 
            $table->foreign('id_estatus')->references('id')->on('gac_cat_estatus_solicitud');

            $table->string('proyecto_sr'); // campo abierto 

            $table->integer('id_empresa'); // id de arjion 

            $table->string('proveedor'); // campo abierto 

            $table->unsignedBigInteger('id_concepto');
            $table->foreign('id_concepto')->references('id')->on('gac_cat_conceptos');

            $table->unsignedBigInteger('id_usuario_revisor')->nullable(); // usuario que revisa la solicitud
            $table->timestamp('fecha_id_usuario_revisor')->nullable(); 
            $table->unsignedBigInteger('id_usuario_autorizador')->nullable(); // usuario que autoriza la solicitud 
            $table->timestamp('fecha_id_usuario_autorizador')->nullable(); 
            $table->unsignedBigInteger('id_usuario_pagada')->nullable(); // usuario de arjion que paga la solicitud 
            $table->timestamp('fecha_id_usuario_pagada')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gac_solicitud');
    }
};
