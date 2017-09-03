<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClientTreatments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tratamentos_cliente', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cliente_id');
            $table->string('nome');
            $table->unsignedInteger('nro_sessoes')->nullable();
            $table->date('data_inicio')->nullable();
            $table->string('alterado_por')->nullable();
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
        Schema::dropIfExists('tratamentos_cliente');
    }
}
