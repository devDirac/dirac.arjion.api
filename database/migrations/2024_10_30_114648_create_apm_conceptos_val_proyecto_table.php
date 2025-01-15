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
        Schema::create('apm_catalogo_valores_proyecto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_valor_proyecto');
            $table->foreign('id_valor_proyecto')->references('id')->on('apm_valores_proyecto');
            $table->string('concepto');
            $table->text('descripcion');
            $table->string('unidad');
            $table->decimal('cantidad', 17, 6);
            $table->decimal('pu', 17, 6);
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->boolean('estatus')->default(true);
            $table->boolean('homologado')->default(true);
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id')->on('users');
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
        Schema::dropIfExists('apm_catalogo_valores_proyecto');
    }
};
