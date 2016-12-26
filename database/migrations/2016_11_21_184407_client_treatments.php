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
            $table->decimal('preco',7,2);
            $table->string('forma_pagamento');
            $table->unsignedInteger('nro_parcelas');
            $table->decimal('taxa_cartao_utilizada',5,4);
            $table->unsignedInteger('nro_sessoes');
            $table->decimal('desconto',7,2);
            $table->timestamp('data_inicio')->nullable();
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
