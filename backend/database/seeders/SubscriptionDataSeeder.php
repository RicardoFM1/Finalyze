<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Period;
use App\Models\Feature;
use Illuminate\Support\Facades\DB;

class SubscriptionDataSeeder extends Seeder
{
    public function run(): void
    {
        // Deactivate all existing plans (don't delete due to foreign keys)
        \App\Models\Plan::query()->update(['is_active' => false]);

        // Periods
        $periods = [
            ['name' => 'Semanal', 'slug' => 'weekly', 'days_count' => 7],
            ['name' => 'Mensal', 'slug' => 'monthly', 'days_count' => 30],
            ['name' => 'Trimestral', 'slug' => 'quarterly', 'days_count' => 90],
            ['name' => 'Anual', 'slug' => 'yearly', 'days_count' => 365],
        ];

        foreach ($periods as $period) {
            Period::updateOrCreate(['slug' => $period['slug']], $period);
        }

        // Default Features
        $featuresList = [
            ['name' => 'Painel Financeiro', 'slug' => 'dashboard', 'description' => 'Acesso ao painel completo'],
            ['name' => 'Lançamentos', 'slug' => 'transactions', 'description' => 'Registrar entradas e saídas'],
            ['name' => 'Relatórios Gráficos', 'slug' => 'reports', 'description' => 'Visualização de gráficos'],
            ['name' => 'Suporte Prioritário', 'slug' => 'support', 'description' => 'Atendimento em menos de 24h'],
        ];

        foreach ($featuresList as $feature) {
            Feature::updateOrCreate(['slug' => $feature['slug']], $feature);
        }

        // Plans
        $planData = [
            [
                'name' => 'Essencial',
                'description' => 'Ideal para quem está começando a se organizar.',
                'max_transactions' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'description' => 'Controle total com relatórios avançados e suporte.',
                'max_transactions' => 1000,
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'description' => 'A experiência completa com IA e exportação de dados.',
                'max_transactions' => 99999,
                'is_active' => true,
            ],
        ];

        foreach ($planData as $data) {
            $plan = \App\Models\Plan::updateOrCreate(['name' => $data['name']], $data);


            foreach (Period::all() as $period) {
                $basePrice = $plan->name === 'Essencial' ? 1990 : ($plan->name === 'Pro' ? 4990 : 9990);

                $price = $basePrice;
                $discount = 0;

                if ($period->slug === 'weekly') $price = round($basePrice / 4);
                if ($period->slug === 'quarterly') {
                    $price = $basePrice * 3 * 0.9;
                    $discount = 10;
                }
                if ($period->slug === 'yearly') {
                    $price = $basePrice * 12 * 0.8;
                    $discount = 20;
                }

                $plan->periods()->syncWithoutDetaching([
                    $period->id => [
                        'price_cents' => (int)$price,
                        'discount_percentage' => $discount
                    ]
                ]);
            }

            // Link some features
            if ($plan->name === 'Essencial') {
                $plan->features()->sync(Feature::whereIn('slug', ['dashboard', 'transactions'])->pluck('id'));
            } elseif ($plan->name === 'Pro') {
                $plan->features()->sync(Feature::whereIn('slug', ['dashboard', 'transactions', 'reports'])->pluck('id'));
            } else {
                $plan->features()->sync(Feature::all()->pluck('id'));
            }
        }
    }
}
