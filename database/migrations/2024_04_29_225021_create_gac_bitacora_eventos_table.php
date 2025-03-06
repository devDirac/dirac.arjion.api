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
        Schema::create('gac_bitacora_eventos', function (Blueprint $table) {
            $table->id();
            $table->string('evento');
            $table->text('descripcion');
            $table->text('tipo');
            $table->unsignedBigInteger('id_tabla');
            $table->unsignedBigInteger('id_ref');
            $table->timestamp('creado')->nullable()->default(now());
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
        Schema::dropIfExists('apm_bitacora_eventos');
    }
};
