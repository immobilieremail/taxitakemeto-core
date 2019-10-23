<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Media;
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

    /**
     * @test
     *
     */
    public function view_media()
    {
        $media = factory(Media::class)->create();
        $response = $this->get(route('media.show', ['medium' => $media->viewFacet->swiss_number]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                "type",
                "path"
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_view_media()
    {
        $response = $this->get(route('media.show', ['medium' => "nimportequoi"]));
        $response
            ->assertStatus(404);
    }

    /**
     * @test
     *
     */
    public function edit_media()
    {
        $media = factory(Media::class)->create();
        $response = $this->get(route('media.edit', ['medium' => $media->editFacet->swiss_number]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(4)
            ->assertJsonStructure([
                "type",
                "view_facet",
                "path",
                "delete"
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_edit_media()
    {
        $response = $this->get(route('media.edit', ['medium' => "dautrechoses"]));
        $response
            ->assertStatus(404);
    }

    /**
     * @test
     *
     */
    public function delete_media()
    {
        $media = factory(Media::class)->create();
        $response = $this->delete(route('media.destroy', ['medium' => $media->editFacet->swiss_number]));
        $response
            ->assertStatus(204);
    }

    /**
     * @test
     *
     */
    public function bad_delete_media()
    {
        $response = $this->delete(route('media.destroy', ['medium' => "badbougieparfumee"]));
        $response
            ->assertStatus(404);
    }
}
