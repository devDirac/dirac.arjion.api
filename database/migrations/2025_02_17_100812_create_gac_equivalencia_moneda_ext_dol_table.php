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
        Schema::create('gac_equivalencia_moneda_ext_dol', function (Blueprint $table) {
            $table->id();
            $table->string('pais');
            $table->string('moneda');
            $table->decimal('valor_en_dolar', 17, 6);
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
        Schema::dropIfExists('gac_equivalencia_moneda_ext_dol');
    }
};
