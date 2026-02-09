<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Verificar assinaturas expiradas diariamente à meia-noite
Schedule::command('app:verificar-assinaturas-expiradas')
    ->daily()
    ->at('00:00')
    ->timezone('America/Sao_Paulo');

// Enviar lembretes de renovação 2x por dia (9h e 18h)
Schedule::command('app:enviar-lembretes-renovacao')
    ->dailyAt('09:00')
    ->timezone('America/Sao_Paulo');

Schedule::command('app:enviar-lembretes-renovacao')
    ->dailyAt('18:00')
    ->timezone('America/Sao_Paulo');
