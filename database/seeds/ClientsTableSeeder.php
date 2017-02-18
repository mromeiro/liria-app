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
            'name' => 'Matheus',
            'email' => 'matheus.romeiro@gmail.com',
            'password' => Hash::make('gordobobo'),
            'role' => 'admin'

        ]);

        DB::table('users')->insert([
            'name' => 'Fulana',
            'email' => 'fulana@email.com',
            'password' => Hash::make('gordobobo'),
            'role' => 'secretaria'
        ]);
    }
}
