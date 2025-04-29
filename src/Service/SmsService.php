<?php

namespace App\Service;

use Twilio\Rest\Client;

class SmsService
{
    public function __construct(
        private string $twilioSid,
        private string $twilioAuthToken,
        private string $twilioPhoneNumber
    ) {}

    public function sendSms(string $to, string $message): void
    {
        $client = new Client($this->twilioSid, $this->twilioAuthToken);
        
        $client->messages->create(
            $to,
            [
                'from' => $this->twilioPhoneNumber,
                'body' => $message
            ]
        );
    }
}