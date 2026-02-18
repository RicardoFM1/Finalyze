<?php

namespace App\Console\Commands;

use Google\Client;
use Google\Service\Gmail;
use Illuminate\Console\Command;

class GmailAuth extends Command
{
    protected $signature = 'gmail:auth';
    protected $description = 'Authenticate with Gmail API to get Refresh Token';

    public function handle()
    {
        $clientId = env('GMAIL_CLIENT_ID');
        $clientSecret = env('GMAIL_CLIENT_SECRET');

        if (!$clientId || !$clientSecret) {
            $this->error('GMAIL_CLIENT_ID and GMAIL_CLIENT_SECRET must be set in .env');
            return;
        }

        $client = new Client();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri('http://localhost:8000/oauth/gmail/callback');
        $client->addScope(Gmail::GMAIL_SEND);
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');

        $authUrl = $client->createAuthUrl();

        $this->info('Open this URL in your browser to authorize:');
        $this->line($authUrl);
        $this->newLine();

        $authCode = $this->ask('Enter the authorization code from the callback URL (code parameter)');

        try {
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            if (array_key_exists('error', $accessToken)) {
                throw new \Exception(join(', ', $accessToken));
            }

            if (isset($accessToken['refresh_token'])) {
                $this->info('Success! Add this to your .env file:');
                $this->warn('GMAIL_REFRESH_TOKEN=' . $accessToken['refresh_token']);
            } else {
                $this->error('No refresh token return. Revoke access in Google Account permissions and try again.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
