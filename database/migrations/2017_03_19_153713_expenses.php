<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Expenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despesas_diversas', function (Blueprint $table) {

            $table->increments('id');
            $table->string('tipo');
            $table->string('descricao');
            $table->date('data_despesa');
            $table->date('data_parcela');
            $table->string('metodo_pagamento');
            $table->string('recibo')->nullable();
            $table->decimal('valor_parcela',10,2);
            $table->decimal('valor_total',10,2);
            $table->string('alterado_por');
            $table->integer('total_parcelas');
            $table->integer('parcela');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('despesas_diversas');
    }
}
