<?php

namespace Tests\Feature;

use App\Audio,
    App\AudioList,
    App\AudioListViewFacet,
    App\AudioListEditFacet;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class APIRouteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function audiolist_entry_point()
    {
        $response       = $this->get('/api/audiolist/create');

        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                'type',
                'ocapType',
                'url'
            ]);
    }

    /** @test */
    public function get_audiolist_view()
    {
        $audiolistWithFacets    = factory(AudioList::class)->create();

        $response   = $this->get(route('audiolist.show', ['audiolist' => $audiolistWithFacets->viewFacet->swiss_number]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                'type',
                'contents'
            ]);
    }

    /** @test */
    public function get_bad_audiolist_view()
    {
        $response   = $this->get(route('audiolist.show', ['audiolist' => \str_random(24)]));
        $response
            ->assertStatus(404);
    }

    /** @test */
    public function get_audiolist_view_with_audio()
    {
        $audiolistWithFacets    = factory(AudioList::class)->create();

        $audiolistWithFacets->editFacet->addAudio('mp3');
        $response   = $this->get(route('audiolist.show', ['audiolist' => $audiolistWithFacets->viewFacet->swiss_number]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                'type',
                'contents' => [
                    [
                        'audio' => [
                            'type',
                            'audio_id',
                            'path_to_file'
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function get_audiolist_edit()
    {
        $audiolistWithFacets    = factory(AudioList::class)->create();

        $response   = $this->get(route('audiolist.edit', ['audiolist' => $audiolistWithFacets->editFacet->swiss_number]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(4)
            ->assertJsonStructure([
                'type',
                'new_audio',
                'view_facet',
                'contents'
            ]);
    }

    /** @test */
    public function get_bad_audiolist_edit()
    {
        $response   = $this->get(route('audiolist.edit', ['audiolist' => \str_random(24)]));
        $response
            ->assertStatus(404);
    }

    /** @test */
    public function get_audiolist_edit_with_audio()
    {
        $audiolistWithFacets    = factory(AudioList::class)->create();

        $audiolistWithFacets->editFacet->addAudio('mp3');
        $response   = $this->get(route('audiolist.edit', ['audiolist' => $audiolistWithFacets->editFacet->swiss_number]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(4)
            ->assertJsonStructure([
                'type',
                'new_audio',
                'view_facet',
                'contents' => [
                    [
                        'audio' => [
                            'type',
                            'audio_id',
                            'path_to_file'
                        ],
                        'update_audio',
                        'delete_audio'
                    ]
                ]
            ]);
    }

    /** @test */
    public function audio_entry_point_bad_audio()
    {
        $audiolistWithFacets    = factory(AudioList::class)->create();

        $response   = $this->post(route('audio.store', ['audiolist' => $audiolistWithFacets->editFacet->swiss_number]), ['audio' => '']);
        $response
            ->assertStatus(302);
    }

    /** @test */
    public function update_bad_audio()
    {
        $response   = $this->put(route('audio.update', [
            'audiolist' => \str_random(24),
            'audio' => \str_random(24)]));
        $response
            ->assertStatus(404);
    }

    /** @test */
    public function destroy_audio()
    {
        $audiolistWithFacets    = factory(AudioList::class)->create();
        $audio                  = $audiolistWithFacets->editFacet->addAudio('mp3');

        Storage::disk('converts')->put($audio->path, '');

        $response   = $this->delete(route('audio.destroy', [
            'audiolist' => $audiolistWithFacets->editFacet->swiss_number,
            'audio' => $audio->swiss_number]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonStructure([
                'status'
            ]);
    }

    /** @test */
    public function destroy_bad_audio()
    {
        $response   = $this->delete(route('audio.destroy', [
            'audiolist' => \str_random(24),
            'audio' => \str_random(24)]));
        $response
            ->assertStatus(404);
    }
}
