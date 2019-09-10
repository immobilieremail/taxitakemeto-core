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
use Symfony\Component\HttpFoundation\File\UploadedFile;

class APIRouteTest extends TestCase
{
    /** @test */
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
    }

    /** @test */
    public function getAudiolistEditWithAudio()
    {
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
    }
}
