<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Payments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tratamento_cliente_id');
            $table->string('nro_parcela');
            $table->timestamp('data_prevista');
            $table->timestamp('data_pagamento')->nullable();
            $table->decimal('valor_parcela',7,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    Schema::dropIfExists('pagamentos');
    }
}
