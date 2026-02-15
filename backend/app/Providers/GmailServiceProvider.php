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
        //
    }

    public function boot(): void
    {
        Mail::extend('gmail', function (array $config = []) {
            $client = new Client();
            $client->setClientId(env('GMAIL_CLIENT_ID'));
            $client->setClientSecret(env('GMAIL_CLIENT_SECRET'));
            $client->setAccessType('offline');
            $client->setApprovalPrompt('force');

            $refreshToken = env('GMAIL_REFRESH_TOKEN');

            if ($refreshToken) {
                $client->refreshToken($refreshToken);
            }

            return new GmailTransport($client);
        });
    }
}
