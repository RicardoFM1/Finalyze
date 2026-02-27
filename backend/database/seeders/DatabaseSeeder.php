<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            SubscriptionDataSeeder::class,
        ]);

        Usuario::factory()->create([
            'nome' => 'Admin User',
            'email' => 'admin@admin.com',
            'senha' => bcrypt('password'),
            'admin' => true
        ]);
    }
}
