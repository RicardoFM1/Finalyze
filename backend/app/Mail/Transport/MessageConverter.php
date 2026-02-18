<?php

namespace App\Mail\Transport;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;

class MessageConverter
{
    public static function toEmail(Message $message): Email
    {
        if ($message instanceof Email) {
            return $message;
        }

        throw new \InvalidArgumentException('Message must be an instance of Symfony\Component\Mime\Email');
    }
}
