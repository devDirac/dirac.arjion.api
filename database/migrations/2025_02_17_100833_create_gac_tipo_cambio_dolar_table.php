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
        Schema::create('gac_tipo_cambio_dolar', function (Blueprint $table) {
            $table->id();
            $table->decimal('pesos_dolar', 17, 6);
            $table->date('fecha');
            $table->timestamp('fecha_registro');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gac_tipo_cambio_dolar');
    }
};
