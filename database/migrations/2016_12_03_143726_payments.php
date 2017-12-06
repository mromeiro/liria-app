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
            $table->integer('id_pagamento_original')->nullable();
            $table->string('descricao');
            $table->string('nro_parcela');
            $table->date('data_prevista')->nullable();
            $table->string('cliente')->nullable();
            $table->date('data_pagamento_efetuado')->nullable();
            $table->date('data_pagamento_confirmado')->nullable();
            $table->decimal('valor_parcela',7,2);
            $table->string('pago')->nullable();
            $table->string('alterado_por')->nullable();
            $table->decimal('valor_bruto',7,2);
            $table->decimal('valor_depois_taxa',7,2)->nullable();
            $table->string('forma_pagamento')->nullable();
            $table->unsignedInteger('nro_parcelas')->nullable();
            $table->decimal('taxa_cartao_utilizada',5,4)->nullable();
            $table->string('id_transacao')->nullable();
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
