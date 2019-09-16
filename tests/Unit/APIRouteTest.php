<?php

namespace Tests\Unit;

use App\Audio,
    App\AudioViewFacet,
    App\AudioEditFacet;

use App\AudioList,
    App\AudioListViewFacet,
    App\AudioListEditFacet;

use App\Shell;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Eris\Generator,
    Eris\TestTrait;

class APIRouteTest extends TestCase
{
    use TestTrait;



    /** Audiolist routes tests */



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
    public function getAudiolistViewButItIsAudiolistEdit()
    {
        $list = AudioList::create();
        $list_edit = AudioListEditFacet::create(['id_list' => $list->id]);
        $list_view = AudioListViewFacet::create(['id_list' => $list->id]);

        $response = $this->get("/api/audiolist/$list_edit->swiss_number");
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

    private function generateAudiosJson($audiolist) : Array
    {
        $random = rand(0, 10);
        $audio_array["audios"] = [];

        for ($i = 0; $i < $random; $i++) {
            $audio = Audio::create(['extension' => 'mp3']);
            $audio_view = AudioViewFacet::create(['id_audio' => $audio->id]);
            $audio_edit = AudioEditFacet::create(['id_audio' => $audio->id]);

            $audiolist->audioViews()->save($audio_view);
            $audiolist->audioEdits()->save($audio_edit);
            $audio_array["audios"][] = [
                'id' => $audio_view->swiss_number
            ];
        }
        return $audio_array;
    }

    /** @test */
    public function updateAudiolist()
    {
        $this->limitTo(10)->forAll(Generator\nat())->then(function () {
            $audiolist = AudioList::create();
            $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);
            $audiolist_view = AudioListViewFacet::create(['id_list' => $audiolist->id]);

            $audio_array = $this->generateAudiosJson($audiolist);
            $response = $this->put("/api/audiolist/$audiolist_edit->swiss_number", ["data" => $audio_array]);
            $mapped_audio = array_map(function ($audio) {
                return [
                    'type' => 'ocap',
                    'ocapType' => 'AudioView',
                    'url' => '/api/audio/' . $audio["id"]
                ];
            }, $audio_array["audios"]);
            $response->assertStatus(200);
            $this->assertEquals(json_encode($response->getData()->contents),
                json_encode($mapped_audio));
        });
    }

    /** @test */
    public function updateAudiolistWithBadDataRequest()
    {
        $audiolist = AudioList::create();
        $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);
        $audiolist_view = AudioListViewFacet::create(['id_list' => $audiolist->id]);

        $response = $this->put("/api/audiolist/$audiolist_edit->swiss_number", ["data" => ["audios" => [["id" => "a"], ["id" => "b"]]]]);
        $response->assertStatus(400);
    }

    /** @test */
    public function updateAudiolistWithBadSomethingRequest()
    {
        $audiolist = AudioList::create();
        $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);
        $audiolist_view = AudioListViewFacet::create(['id_list' => $audiolist->id]);

        $response = $this->put("/api/audiolist/$audiolist_edit->swiss_number", ["b" => ["n" => [["id" => "cho"], ["id" => "co"]]]]);
        $response->assertStatus(400);
    }

    /** @test */
    public function updateNonExistantAudiolist()
    {
        $random_number = '4da7848daj';

        $response = $this->put("/api/audiolist/$random_number", ["data" => ["audios" => [["id" => "a"], ["id" => "b"]]]]);
        $response->assertStatus(404);
    }



    /** Audio routes tests */



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



    /** Shell routes tests */



    /** @test */
    public function postShell()
    {
        $response = $this->post('/api/shell');
        $response->assertStatus(200);
    }

    /** @test */
    public function getShell()
    {
        $shell = Shell::create();

        $response = $this->get("/api/shell/$shell->swiss_number");
        $response->assertStatus(200);
    }
}
