<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Periodo;
use App\Models\Recurso;
use App\Models\Plano;
use Illuminate\Support\Facades\DB;

class SubscriptionDataSeeder extends Seeder
{
    public function run(): void
    {
        // Deactivate all existing plans (don't delete due to foreign keys)
        Plano::query()->update(['ativo' => false]);

        // Periods
        $periodos = [
            ['nome' => 'Semanal', 'slug' => 'semanal', 'quantidade_dias' => 7],
            ['nome' => 'Mensal', 'slug' => 'mensal', 'quantidade_dias' => 30],
            ['nome' => 'Trimestral', 'slug' => 'trimestral', 'quantidade_dias' => 90],
            ['nome' => 'Anual', 'slug' => 'anual', 'quantidade_dias' => 365],
        ];

        foreach ($periodos as $periodo) {
            Periodo::updateOrCreate(['slug' => $periodo['slug']], $periodo);
        }


        $recursosLista = [
            ['nome' => 'Painel Financeiro', 'slug' => 'painel', 'descricao' => 'Acesso ao painel completo'],
            ['nome' => 'Lançamentos', 'slug' => 'lancamentos', 'descricao' => 'Registrar entradas e saídas'],
            ['nome' => 'Relatórios Gráficos', 'slug' => 'relatorios', 'descricao' => 'Visualização de gráficos'],

        ];

        foreach ($recursosLista as $recurso) {
            Recurso::updateOrCreate(['slug' => $recurso['slug']], $recurso);
        }

        // Plans
        $planoDados = [
            [
                'nome' => 'Essencial',
                'descricao' => 'Ideal para quem está começando a se organizar.',
                'limite_lancamentos' => 100,
                'ativo' => true,
            ],
            [
                'nome' => 'Pro',
                'descricao' => 'Controle total com relatórios avançados e suporte.',
                'limite_lancamentos' => 1000,
                'ativo' => true,
            ],
            [
                'nome' => 'Premium',
                'descricao' => 'A experiência completa com IA e exportação de dados.',
                'limite_lancamentos' => 99999,
                'ativo' => true,
            ],
        ];

        foreach ($planoDados as $dados) {
            $plano = Plano::updateOrCreate(['nome' => $dados['nome']], $dados);


            foreach (Periodo::all() as $periodo) {
                $basePrice = $plano->nome === 'Essencial' ? 1990 : ($plano->nome === 'Pro' ? 4990 : 9990);

                $preco = $basePrice;
                $desconto = 0;

                if ($periodo->slug === 'semanal') $preco = round($basePrice / 4);
                if ($periodo->slug === 'trimestral') {
                    $preco = $basePrice * 3 * 0.9;
                    $desconto = 10;
                }
                if ($periodo->slug === 'anual') {
                    $preco = $basePrice * 12 * 0.8;
                    $desconto = 20;
                }

                $plano->periodos()->syncWithoutDetaching([
                    $periodo->id => [
                        'valor_centavos' => (int)$preco,
                        'percentual_desconto' => $desconto
                    ]
                ]);
            }


            if ($plano->nome === 'Essencial') {
                $plano->recursos()->sync(Recurso::whereIn('slug', ['painel', 'lancamentos'])->pluck('id'));
            } elseif ($plano->nome === 'Pro') {
                $plano->recursos()->sync(Recurso::whereIn('slug', ['painel', 'lancamentos', 'relatorios'])->pluck('id'));
            } else {
                $plano->recursos()->sync(Recurso::all()->pluck('id'));
            }
        }
    }
}
