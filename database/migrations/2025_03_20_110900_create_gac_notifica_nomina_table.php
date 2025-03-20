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
        Schema::create('gac_notifica_nomina', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_solicitud');
            $table->foreign('id_solicitud')->references('id')->on('gac_solicitud');
            $table->decimal('importe', 17, 6);
            $table->date('fecha_solicitud')->nullable();
            $table->timestamp('fecha_registro')->nullable();
            $table->unsignedBigInteger('id_usuario_notifica');
            $table->unsignedBigInteger('id_usuario_recive_notificacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gac_notifica_nomina');
    }
};
