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
            $table->decimal('preco_final',7,2)->nullable();
            $table->string('forma_pagamento')->nullable();
            $table->unsignedInteger('nro_parcelas')->nullable();
            $table->decimal('taxa_cartao_utilizada',5,4)->nullable();
            $table->unsignedInteger('nro_sessoes')->nullable();
            $table->decimal('desconto',7,2)->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_primeira_parcela')->nullable();
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
