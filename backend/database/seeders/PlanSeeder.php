<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('planos')->insert([
            [
                'name' => 'Básico',
                'price' => 29.90,
                'interval' => 'month',
                'features' => json_encode(['Controle de Receitas', 'Controle de Despesas', 'Relatórios Básicos', '50 Lançamentos/mês']),
                'limite_lancamentos' => 50,
                'description' => 'Perfeito para começar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro',
                'price' => 59.90,
                'interval' => 'month',
                'features' => json_encode(['Lançamentos Ilimitados', 'Relatórios Avançados', 'Exportar PDF', 'Suporte Prioritário']),
                'limite_lancamentos' => 999999,
                'description' => 'Para poupadores sérios',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Anual Pro',
                'price' => 599.00,
                'interval' => 'year',
                'features' => json_encode(['Todos recursos Pro', '2 Meses Grátis', 'Chat com Consultor']),
                'limite_lancamentos' => 999999,
                'description' => 'Melhor Valor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
