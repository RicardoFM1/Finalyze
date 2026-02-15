<?php
// Forced sync comment


namespace App\Mail\Transport;

use Google\Client;
use Google\Service\Gmail as GmailService;
use Google\Service\Gmail\Message as GmailMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Email;

class GmailTransport extends AbstractTransport
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function doSend(SentMessage $message): void
    {
        $service = new GmailService($this->client);

        $rawMessage = $this->getRawMessage($message);

        $msg = new GmailMessage();
        $msg->setRaw($rawMessage);

        try {
            $service->users_messages->send('me', $msg);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function getRawMessage(SentMessage $message): string
    {
        return strtr(base64_encode($message->toString()), array('+' => '-', '/' => '_'));
    }

    public function __toString(): string
    {
        return 'gmail';
    }
}
