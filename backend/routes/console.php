<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:verificar-assinaturas-expiradas')
    ->daily()
    ->at('00:00')
    ->timezone('America/Sao_Paulo');

Schedule::command('app:enviar-avisos-renovacao')
    ->dailyAt('09:00')
    ->timezone('America/Sao_Paulo');

Schedule::command('app:enviar-avisos-renovacao')
    ->dailyAt('18:00')
    ->timezone('America/Sao_Paulo');

Schedule::command('app:enviar-lembretes-pessoais')
    ->everyMinute()
    ->timezone('America/Sao_Paulo');
