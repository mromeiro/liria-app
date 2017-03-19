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
            $table->string('metodo_pagamento');
            $table->string('recibo');
            $table->string('alterado_por');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('despesas');
    }
}
