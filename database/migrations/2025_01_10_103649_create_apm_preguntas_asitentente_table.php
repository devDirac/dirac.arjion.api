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
        Schema::create('apm_preguntas_asitentente', function (Blueprint $table) {
            $table->id();
            $table->text('pregunta');
            $table->longText('embedding');
            $table->foreign('id_contenido')->references('id')->on('contenido_embeddings');
            $table->unsignedBigInteger('id_contenido');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_preguntas_asitentente');
    }
};
