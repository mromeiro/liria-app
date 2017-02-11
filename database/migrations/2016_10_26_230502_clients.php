<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Clients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('sobrenome')->nullable();
            $table->string('cpf')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('cep')->nullable();
            $table->string('rua')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->string('tel_fixo')->nullable();
            $table->string('celular')->nullable();
            $table->string('email')->nullable();
            $table->text('obs')->nullable();
            $table->string('imagem')->nullable();
            $table->string('foto')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('clientes');
    }
}
