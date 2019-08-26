<?php

namespace Tests\Feature;

use App\Audio,
    App\AudioList,
    App\AudioListEditFacet;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class APIRouteTest extends TestCase
{
    /** @test */
    public function audiolistEntryPoint()
    {
        $keys_to_test = array('type', 'ocapType', 'url');

        $result = $this->post('/api/audiolist');
        $json_decode = json_decode($result->getContent());

        foreach ($keys_to_test as $key)
            $this->assertTrue(array_key_exists($key, $json_decode));
    }

    /** @test */
    public function getAudiolistEdit()
    {
        $audiolist = AudioList::create();
        $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);
        $keys_to_test = array('type', 'new_audio', 'contents');

        $result = $this->get("/api/audiolist_edit/$audiolist_edit->swiss_number");
        $json_decode = json_decode($result->getContent());

        foreach ($keys_to_test as $key)
            $this->assertTrue(array_key_exists($key, $json_decode));
    }

    /** @test */
    public function getAudiolistEditWithAudio()
    {
        $audiolist = AudioList::create();
        $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);
        $keys_to_test = array('audio', 'update_audio', 'delete_audio');
        $audio_keys_to_test = array('type', 'audio_id', 'path_to_file');

        $audiolist_edit->addAudio('mp3');
        $result = $this->get("/api/audiolist_edit/$audiolist_edit->swiss_number");
        $json_decode = json_decode($result->getContent());

        foreach ($keys_to_test as $key)
            $this->assertTrue(array_key_exists($key, $json_decode->contents[0]));
        foreach ($audio_keys_to_test as $key)
            $this->assertTrue(array_key_exists($key, $json_decode->contents[0]->audio));
    }

    /** @test */
    public function postAudiolistEditNewAudio()
    {
        $audiolist = AudioList::create();
        $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);
        $file = new UploadedFile("/home/louis/Musique/applause.wav", "applause.wav", "audio/x-wav", 0);
        $keys_to_test = array('type', 'audio_id', 'path_to_file');

        $count_before = Audio::all()->count();
        $response = $this->post("/api/audiolist_edit/$audiolist_edit->swiss_number/new_audio", ['audio' => $file]);
        $json_decode = json_decode($response->getContent());

        $count_after = Audio::all()->count();
        $this->assertEquals($count_before + 1, $count_after);
        foreach ($keys_to_test as $key)
            $this->assertTrue(array_key_exists($key, $json_decode));
    }
}
