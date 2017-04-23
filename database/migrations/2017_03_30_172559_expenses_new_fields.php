<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExpensesNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('despesas_diversas', function (Blueprint $table) {
            $table->unsignedInteger('parcela');
            $table->unsignedInteger('nro_parcelas');
            $table->date('mes_parcela');
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
        //
    }
}
