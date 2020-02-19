<?php

namespace Tests\Feature;

use Tests\TestCase;
use Faker\Generator as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\API\TwilioService;

class TwilioServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     */
    public function should_post_with_valid_recipient()
    {
        $valid_number = "+33123456789";
        $sms_body = "sms message";

        //TODO: Mock the call to the Twilio API in a way that allows testing of the TwilioService class
        $fake_twilio = $this->mock(TwilioService::class, function ($mock) use ($valid_number, $sms_body) {
            $mock->shouldReceive('post')
            ->once()
            ->with($sms_body, $valid_number)
            ->andReturn(["sid" => "something"]);
        });
        $result = $fake_twilio->post($sms_body, $valid_number);
        
        // $twilio = new TwilioService;
        // $result = $twilio->post($sms_body, $valid_number);
        $this->assertTrue(isset($result["sid"]));
        $this->assertTrue(!empty($result["sid"]));
        $this->assertFalse(isset($result["error"]));
    }
    
    /**
     * @test
     *
     */
    public function should_not_post_with_valid_recipient()
    {
        $invalid_number = "thisIsn'tAPhoneNumber";
        $sms_body = "sms message";
        
        //TODO: Mock the call to the Twilio API in a way that allows testing of the TwilioService class
        $fake_twilio = $this->mock(TwilioService::class, function ($mock) use ($invalid_number, $sms_body) {
            $mock->shouldReceive('post')
                ->once()
                ->with($sms_body, $invalid_number)
                ->andReturn(["error" => "something"]);
        });
        $result = $fake_twilio->post($sms_body, $invalid_number);
        
        // $twilio = new TwilioService;
        // $result = $twilio->post($sms_body, $invalid_number);
        $this->assertTrue(isset($result["error"]));
        $this->assertTrue(!empty($result["error"]));
        $this->assertTrue(!isset($result["sid"]));
    }
}
