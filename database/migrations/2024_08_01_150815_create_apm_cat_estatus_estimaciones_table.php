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
        Schema::create('apm_cat_estatus_estimaciones', function (Blueprint $table) {
            $table->id();
           $table->string('evento');
           $table->string('descripcion');
           $table->boolean('estatus')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apm_cat_estatus_estimaciones');
    }
};
