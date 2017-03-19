<?php

use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            'name' => 'Nathaly',
            'email' => 'nathaly_biomedicina@hotmail.com',
            'password' => Hash::make('273273Nlb'),
            'role' => 'admin'

        ]);

        DB::table('users')->insert([
            'name' => 'Amanda',
            'email' => 'dranathalybrandao@gmail.com',
            'password' => Hash::make('doutoranlbr2017'),
            'role' => 'secretaria'
        ]);
    }
}
