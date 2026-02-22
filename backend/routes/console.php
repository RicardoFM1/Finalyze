<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:verificar-assinaturas-expiradas')
    ->everyThirtyMinutes()
    ->timezone('America/Sao_Paulo');

Schedule::command('app:enviar-avisos-renovacao')
    ->everyThirtyMinutes()
    ->timezone('America/Sao_Paulo');

Schedule::command('app:enviar-lembretes-pessoais')
    ->everyMinute()
    ->timezone('America/Sao_Paulo');

Schedule::command('app:enviar-notificacoes-metas')
    ->everyMinute()
    ->timezone('America/Sao_Paulo');
