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
    public function audio_list_entry_point()
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
        $this->assertEquals($audioListCount, 1);
    }

    /** @test */
    public function get_audio_list_edit_and_view_facets()
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
        $audiolist = AudioList::create();
        $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);
        $audiolist_view = AudioListViewFacet::create(['id_list' => $audiolist->id]);
        $keys_to_test = array('audio', 'update_audio', 'delete_audio');
        $audio_keys_to_test = array('type', 'audio_id', 'path_to_file');

        $audiolist_edit->addAudio('mp3');
        $result = $this->get("/api/audiolist/$audiolist_edit->swiss_number/edit");
        $json_decode = json_decode($result->getContent());

        foreach ($keys_to_test as $key)
            $this->assertTrue(array_key_exists($key, $json_decode->contents[0]));
        foreach ($audio_keys_to_test as $key)
            $this->assertTrue(array_key_exists($key, $json_decode->contents[0]->audio));
    }

    public function postAudiolistEditNewAudio()
    {
        $audiolist = AudioList::create();
        $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);
        $file = new UploadedFile("/home/louis/Musique/applause.wav", "applause.wav", "audio/x-wav", 0);
        $keys_to_test = array('type', 'audio_id', 'path_to_file');

        $count_before = Audio::all()->count();
        $response = $this->post("/api/audiolist/$audiolist_edit->swiss_number/audio", ['audio' => $file]);
        $json_decode = json_decode($response->getContent());

        $count_after = Audio::all()->count();
        $this->assertEquals($count_before + 1, $count_after);
        foreach ($keys_to_test as $key)
            $this->assertTrue(array_key_exists($key, $json_decode));
    }
}
