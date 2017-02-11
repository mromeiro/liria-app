<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CardTax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxa_cartao', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bandeira');
            $table->string('forma_pagamento');
            $table->integer('nro_parcelas_inicio');
            $table->integer('nro_parcelas_fim');
            $table->decimal('taxa',5,4);
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
        Schema::dropIfExists('taxa_cartao');
    }
}
