<?php

namespace App\Services\API;

use Twilio\Rest\Client;
use Twilio\Exceptions\RestException;

class TwilioService {

    private $origin_number;
    private $sid;
    private $token;
    private $twilio;

    function __construct() {
        $this->origin_number = env("TWILIO_ORIGIN_NUMBER");
        $this->sid = env("TWILIO_ACCOUNT_SID");
        $this->token = env("TWILIO_AUTH_TOKEN");
        $this->twilio = new Client($this->sid, $this->token);
    }

    public function post(string $body, string $recipient) {
        try {
            $message = $this->twilio->messages
                        ->create($recipient, 
                        [
                            "body" => $body,
                            "from" => $this->origin_number
                        ]);
        } catch (RestException $e) {
            return $e;
        }
        return $message->sid;
    }
}
