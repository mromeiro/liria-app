<?php

use Illuminate\Database\Seeder;

class CardTaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('taxa_cartao')->insert([
            'bandeira' => 'Mastercard',
            'forma_pagamento' => 'Débito',
            'nro_parcelas_inicio' => 1,
            'nro_parcelas_fim' => 1,
            'taxa' => 0.023,
            'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('taxa_cartao')->insert([
            'bandeira' => 'Mastercard',
            'forma_pagamento' => 'Crédito',
            'nro_parcelas_inicio' => 1,
            'nro_parcelas_fim' => 1,
            'taxa' => 0.031,
            'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('taxa_cartao')->insert([
            'bandeira' => 'Mastercard',
            'forma_pagamento' => 'Crédito',
            'nro_parcelas_inicio' => 2,
            'nro_parcelas_fim' => 6,
            'taxa' => 0.039,
            'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('taxa_cartao')->insert([
            'bandeira' => 'Mastercard',
            'forma_pagamento' => 'Crédito',
            'nro_parcelas_inicio' => 7,
            'nro_parcelas_fim' => 12,
            'taxa' => 0.049,
            'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('taxa_cartao')->insert([
            'bandeira' => 'Visa',
            'forma_pagamento' => 'Débito',
            'nro_parcelas_inicio' => 1,
            'nro_parcelas_fim' => 1,
            'taxa' => 0.023,
            'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('taxa_cartao')->insert([
            'bandeira' => 'Visa',
            'forma_pagamento' => 'Crédito',
            'nro_parcelas_inicio' => 1,
            'nro_parcelas_fim' => 1,
            'taxa' => 0.031,
            'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('taxa_cartao')->insert([
            'bandeira' => 'Visa',
            'forma_pagamento' => 'Crédito',
            'nro_parcelas_inicio' => 2,
            'nro_parcelas_fim' => 6,
            'taxa' => 0.039,
            'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('taxa_cartao')->insert([
            'bandeira' => 'Visa',
            'forma_pagamento' => 'Crédito',
            'nro_parcelas_inicio' => 7,
            'nro_parcelas_fim' => 12,
            'taxa' => 0.049,
            'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
