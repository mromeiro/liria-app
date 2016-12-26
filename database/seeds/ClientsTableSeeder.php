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
            'name' => 'matheus',
            'email' => 'matheus.romeiro@gmail.com',
            'password' => Hash::make('gordobobo')

        ]);
    }
}
