<?php

namespace Tests\Feature;

use App\Audio,
    App\AudioViewFacet,
    App\AudioEditFacet;

use App\AudioList,
    App\AudioListViewFacet,
    App\AudioListEditFacet;

use Tests\TestCase;
use Illuminate\Http\Request;
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
<<<<<<< HEAD
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
=======
    public function getAudiolistViewWithAudio()
    {
        $audio = Audio::create(['extension' => 'wav']);
        $audio_edit = AudioEditFacet::create(['id_audio' => $audio->id]);
        $audio_view = AudioViewFacet::create(['id_audio' => $audio->id]);

        $list = AudioList::create();
        $list_edit = AudioListEditFacet::create(['id_list' => $list->id]);
        $list_view = AudioListViewFacet::create(['id_list' => $list->id]);

        $keys_to_test = array('type', 'id', 'contents');
        $audio_keys_to_test = array('type', 'ocapType', 'url');

        $audio_edit->lists()->save($list);
        $audio_view->lists()->save($list);
        $result = $this->get("/api/audiolist/$list_view->swiss_number");
        $json_decode = json_decode($result->getContent());

        foreach ($keys_to_test as $key)
            $this->assertTrue(array_key_exists($key, $json_decode), "$key");
        foreach ($audio_keys_to_test as $key)
            $this->assertTrue(array_key_exists($key, $json_decode->contents[0]), "$key");
>>>>>>> Add tests for Audio - AudioList relationships
    }

    /** @test */
    public function destroy_audio()
    {
<<<<<<< HEAD
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
=======
        $audio = Audio::create(['extension' => 'wav']);
        $audio_edit = AudioEditFacet::create(['id_audio' => $audio->id]);
        $audio_view = AudioViewFacet::create(['id_audio' => $audio->id]);

        $list = AudioList::create();
        $list_edit = AudioListEditFacet::create(['id_list' => $list->id]);
        $list_view = AudioListViewFacet::create(['id_list' => $list->id]);

        $keys_to_test = array('type', 'id', 'view_facet', 'contents');
        $audio_keys_to_test = array('type', 'ocapType', 'url');

        $audio_edit->lists()->save($list);
        $audio_view->lists()->save($list);
        $result = $this->get("/api/audiolist/$list_edit->swiss_number/edit");
        $json_decode = json_decode($result->getContent());

        foreach ($keys_to_test as $key)
            $this->assertTrue(array_key_exists($key, $json_decode), "$key");
        foreach ($audio_keys_to_test as $key)
            $this->assertTrue(array_key_exists($key, $json_decode->contents[0]), "$key");
>>>>>>> Add tests for Audio - AudioList relationships
    }
}
