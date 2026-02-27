<?php

namespace App\Providers;

use App\Mail\Transport\GmailTransport;
use Google\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class GmailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
    }

    public function boot(): void
    {
        Mail::extend('gmail', function (array $config = []) {
            $client = new Client();
            $client->setClientId(config('services.gmail.client_id'));
            $client->setClientSecret(config('services.gmail.client_secret'));
            $client->setAccessType('offline');
            $client->setApprovalPrompt('force');

            $refreshToken = config('services.gmail.refresh_token');

            if ($refreshToken) {
                $client->refreshToken($refreshToken);
            }

            return new GmailTransport($client);
        });
    }
}
