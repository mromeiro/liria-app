<?php

use Illuminate\Database\Seeder;

class TreatmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tratamentos')->insert([
            'nome' => 'Preenchimento',
            'preco' => 900.00
        ]);

        DB::table('tratamentos')->insert([
            'nome' => 'Botox',
            'preco' => 900.00
        ]);

        DB::table('tratamentos')->insert([
            'nome' => 'Limpeza de pele',
            'preco' => 750.00
        ]);
    }
}
