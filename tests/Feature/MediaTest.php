<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MediaTest extends TestCase
{
    /**
     * @test
     *
     */
    public function create_media()
    {
        $response = $this->post(route('media.store'), ['media' => fopen('/home/mohamed/Téléchargements/applause.wav', 'r')]);
        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                "type",
                "ocapType",
                "url"
            ]);
    }

    /**
     * @test
     *
     */
    public function create_bad_media()
    {
        $response = $this->post(route('media.store'), ['media' => "('/home/mohamed/Téléchargements/applause.wav')"]);
        $response
            ->assertStatus(415);
    }

    /**
     * @test
     *
     */
    public function create_bad_request_media()
    {
        $response = $this->post(route('media.store'), ['string' => "('/home/mohamed/Téléchargements/applause.wav')"]);
        $response
            ->assertStatus(400);
    }
}
