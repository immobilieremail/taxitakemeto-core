<?php

namespace Tests\Unit;

use App\Audio,
    App\AudioViewFacet,
    App\AudioEditFacet;

use App\AudioList,
    App\AudioListViewFacet,
    App\AudioListEditFacet;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class APIRouteTest extends TestCase
{
    /** @test */
    public function postAudiolist()
    {
        $response = $this->post('/api/audiolist');

        return $response->assertStatus(200);
    }

    /** @test */
    public function getAudiolistView()
    {
        $list = AudioList::create();
        $list_edit = AudioListEditFacet::create(['id_list' => $list->id]);
        $list_view = AudioListViewFacet::create(['id_list' => $list->id]);

        $response = $this->get("/api/audiolist/$list_view->swiss_number");
        $response->assertStatus(200);
    }

    /** @test */
    public function getNonExistantAudiolistView()
    {
        $random_string = "89da8a94pw";
        $response = $this->get("/api/audiolist/$random_string");
        $response->assertStatus(404);
    }

    /** @test */
    public function getAudiolistEdit()
    {
        $list = AudioList::create();
        $list_edit = AudioListEditFacet::create(['id_list' => $list->id]);
        $list_view = AudioListViewFacet::create(['id_list' => $list->id]);

        $response = $this->get("/api/audiolist/$list_edit->swiss_number/edit");
        $response->assertStatus(200);
    }

    /** @test */
    public function getNonExistantAudiolistEdit()
    {
        $random_string = "a849a834fb";
        $response = $this->get("/api/audiolist/$random_string/edit");
        $response->assertStatus(404);
    }

    /** @test */
    public function addAudioToAudioList()
    {
        $list = AudioList::create();
        $list_edit = AudioListEditFacet::create(['id_list' => $list->id]);
        $list_view = AudioListViewFacet::create(['id_list' => $list->id]);

        $audio = Audio::create(['extension' => 'mp3']);
        $audio_edit = AudioEditFacet::create(['id_audio' => $audio->id]);
        $audio_view = AudioViewFacet::create(['id_audio' => $audio->id]);

        $response = $this->post("/api/audiolist/$list_edit->swiss_number/add_audio", ['audio' => $audio_view->swiss_number]);
        $response->assertStatus(200);
    }

    /** @test */
    public function addNonExistantAudioToAudioList()
    {
        $list = AudioList::create();
        $list_edit = AudioListEditFacet::create(['id_list' => $list->id]);
        $list_view = AudioListViewFacet::create(['id_list' => $list->id]);

        $random_string = "0tga897qa1";

        $response = $this->post("/api/audiolist/$list_edit->swiss_number/add_audio", ['audio' => $random_string]);
        $response->assertStatus(400);
    }

    /** @test */
    public function addAudioFromNonExistantAudioFormRequestToAudioList()
    {
        $list = AudioList::create();
        $list_edit = AudioListEditFacet::create(['id_list' => $list->id]);
        $list_view = AudioListViewFacet::create(['id_list' => $list->id]);

        $random_string = "0tga897qa1";

        $response = $this->post("/api/audiolist/$list_edit->swiss_number/add_audio", ['oui' => $random_string]);
        $response->assertStatus(400);
    }

    /** @test */
    public function addAudioToNonExistantAudioList()
    {
        $random_string1 = "0tga897qa1";
        $random_string2 = "1yhz908sz2";

        $response = $this->post("/api/audiolist/$random_string1/add_audio", ['oui' => $random_string2]);
        $response->assertStatus(404);
    }

    /** @test */
    public function removeAudioFromAudioList()
    {
        $audio = Audio::create(['extension' => 'mp3']);
        $audio_edit = AudioEditFacet::create(['id_audio' => $audio->id]);
        $audio_view = AudioViewFacet::create(['id_audio' => $audio->id]);

        $list = AudioList::create();
        $list_edit = AudioListEditFacet::create(['id_list' => $list->id]);
        $list_view = AudioListViewFacet::create(['id_list' => $list->id]);

        $list->audioViews()->save($audio_view);

        $response = $this->post("/api/audiolist/$list_edit->swiss_number/remove_audio", ['audio' => $audio_view->swiss_number]);
        $response->assertStatus(200);
    }

    /** @test */
    public function removeNonExistantAudioFromAudioList()
    {
        $list = AudioList::create();
        $list_edit = AudioListEditFacet::create(['id_list' => $list->id]);
        $list_view = AudioListViewFacet::create(['id_list' => $list->id]);

        $random_number = '54dalai247';

        $response = $this->post("/api/audiolist/$list_edit->swiss_number/remove_audio", ['audio' => $random_number]);
        $response->assertStatus(400);
    }

    /** @test */
    public function removeNonLinkedAudioFromAudioList()
    {
        $audio = Audio::create(['extension' => 'mp3']);
        $audio_edit = AudioEditFacet::create(['id_audio' => $audio->id]);
        $audio_view = AudioViewFacet::create(['id_audio' => $audio->id]);

        $list = AudioList::create();
        $list_edit = AudioListEditFacet::create(['id_list' => $list->id]);
        $list_view = AudioListViewFacet::create(['id_list' => $list->id]);

        $response = $this->post("/api/audiolist/$list_edit->swiss_number/remove_audio", ['audio' => $audio_view->swiss_number]);
        $response->assertStatus(200);
    }

    /** @test */
    public function getAudioView()
    {
        $audio = Audio::create(['extension' => 'mp3']);
        $audio_edit = AudioEditFacet::create(['id_audio' => $audio->id]);
        $audio_view = AudioViewFacet::create(['id_audio' => $audio->id]);

        $response = $this->get("/api/audio/$audio_view->swiss_number");
        $response->assertStatus(200);
    }

    /** @test */
    public function getNonExistantAudioView()
    {
        $random_number = "089avg4w6";
        $response = $this->get("/api/audio/$random_number");
        $response->assertStatus(404);
    }

    /** @test */
    public function getAudioEdit()
    {
        $audio = Audio::create(['extension' => 'mp3']);
        $audio_edit = AudioEditFacet::create(['id_audio' => $audio->id]);
        $audio_view = AudioViewFacet::create(['id_audio' => $audio->id]);

        $response = $this->get("/api/audio/$audio_edit->swiss_number/edit");
        $response->assertStatus(200);
    }

    /** @test */
    public function getNonExistantAudioEdit()
    {
        $random_number = "06745aha54";
        $response = $this->get("/api/audio/$random_number/edit");
        $response->assertStatus(404);
    }

    /** @test */
    public function deleteAudio()
    {
        $audio = Audio::create(['extension' => 'mp3']);
        $audio_edit = AudioEditFacet::create(['id_audio' => $audio->id]);
        $audio_view = AudioViewFacet::create(['id_audio' => $audio->id]);

        $file = "public/storage/converts/$audio->path";
        $handle = fopen($file, 'w');
        fclose($handle);

        $response = $this->delete("/api/audio/$audio_edit->swiss_number");
        $response->assertStatus(200);
    }

    /** @test */
    public function deleteNonExistantAudioFile()
    {
        $audio = Audio::create(['extension' => 'mp3']);
        $audio_edit = AudioEditFacet::create(['id_audio' => $audio->id]);
        $audio_view = AudioViewFacet::create(['id_audio' => $audio->id]);

        $response = $this->delete("/api/audio/$audio_edit->swiss_number");
        $response->assertStatus(404);
    }

    /** @test */
    public function deleteNonExistantAudioFromNonExisantAudioFacet()
    {
        $random_number = "ajc5a8pfb0";
        $response = $this->delete("/api/audio/$random_number");
        $response->assertStatus(404);
    }
}
