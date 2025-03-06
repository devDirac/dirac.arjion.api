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
        Schema::create('gac_documentos_solicitud', function (Blueprint $table) {
            $table->id();
            $table->decimal('importe', 17, 6);
            $table->string('nombre_corto'); 
            $table->text('descripcion'); 
            $table->text('ruta'); 
            $table->boolean('estatus')->default(true);
            $table->string('tipo_moneda'); 
            $table->string('documento_valido'); 
            $table->longText('descripcion_documento_validado'); 
            $table->string('nombre_documento'); 
            $table->unsignedBigInteger('id_solicitud');
            $table->foreign('id_solicitud')->references('id')->on('gac_solicitud');
            $table->unsignedBigInteger('id_usuario');
            $table->timestamp('fecha_registro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gac_documentos_solicitud');
    }
};
