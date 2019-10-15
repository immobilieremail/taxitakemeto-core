<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Media;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

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
    public function edit_facet_media()
    {
        $media = factory(Media::class)->create();
        $response = $this->get(route('media.show', ['medium' => $media->editFacet->swiss_number]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                "type",
                "view_facet",
                "path",
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_edit_facet_media()
    {
        $response = $this->get(route('media.show', ['medium' => "dautreschoses"]));
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
