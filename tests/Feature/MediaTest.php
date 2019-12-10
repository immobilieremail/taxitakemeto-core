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
        $response = $this->get(route('obj.show', ['obj' => $media->viewFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                "type",
                "media_type",
                "path"
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_view_media()
    {
        $response = $this->get(route('obj.show', ['obj' => "nimportequoi"]));
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
        $response = $this->get(route('obj.show', ['obj' => $media->editFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(4)
            ->assertJsonStructure([
                "type",
                "view_facet",
                "media_type",
                "path"
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_edit_facet_media()
    {
        $response = $this->get(route('obj.show', ['obj' => "dautreschoses"]));
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
        $response = $this->delete(route('obj.destroy', ['obj' => $media->editFacet->id]));
        $response
            ->assertStatus(204);
    }

    /**
     * @test
     *
     */
    public function bad_delete_media()
    {
        $response = $this->delete(route('obj.destroy', ['obj' => "badbougieparfumee"]));
        $response
            ->assertStatus(404);
    }
}
