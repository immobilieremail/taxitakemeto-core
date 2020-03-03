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
        $twilio = new TwilioService;

        $result = $twilio->post($sms_body, $valid_number);
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
        $twilio = new TwilioService;

        $result = $twilio->post($sms_body, $invalid_number);
        $this->assertTrue(isset($result["error"]));
        $this->assertTrue(!empty($result["error"]));
        $this->assertTrue(!isset($result["sid"]));
    }
}
