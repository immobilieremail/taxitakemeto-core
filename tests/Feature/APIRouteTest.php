<?php

namespace Tests\Feature;

use App\Audio,
    App\AudioList,
    App\AudioListViewFacet,
    App\AudioListEditFacet;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class APIRouteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function audiolist_entry_point()
    {
        $response       = $this->get('/api/audiolist/create');
        $audioListCount = AudioList::all()->count();

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
}
