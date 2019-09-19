<?php

namespace Tests\Unit;

use App\Audio,
    App\AudioViewFacet,
    App\AudioEditFacet;

use App\AudioList,
    App\AudioListViewFacet,
    App\AudioListEditFacet;

use App\Shell,
    App\ShellUserFacet,
    App\ShellDropboxFacet;

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

    private function generateAudiosJson($audiolist, $random) : Array
    {
        $audio_array["audios"] = [];

        for ($i = 0; $i < $random; $i++) {
            $audio = Audio::create(['extension' => 'mp3']);
            $audio_view = AudioViewFacet::create(['id_audio' => $audio->id]);
            $audio_edit = AudioEditFacet::create(['id_audio' => $audio->id]);

            $audio_array["audios"][] = [
                'id' => $audio_view->swiss_number
            ];
        }
        return $audio_array;
    }

    /** @test */
    public function updateAudiolist()
    {
        $this->limitTo(50)->forAll(Generator\nat())->then(function ($random) {
            $audiolist = AudioList::create();
            $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);
            $audiolist_view = AudioListViewFacet::create(['id_list' => $audiolist->id]);

            $audio_array = $this->generateAudiosJson($audiolist, $random);
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

        /** Create empty file */
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
        $shell_user = ShellUserFacet::create(['id_shell' => $shell->id]);
        $shell_dropbox = ShellDropboxFacet::create(['id_shell' => $shell->id]);

        $response = $this->get("/api/shell/$shell_user->swiss_number");
        $response->assertStatus(200);
    }

    /** @test */
    public function accessToAudioListThroughShell()
    {
        $shell = Shell::create();
        $shell_user = ShellUserFacet::create(['id_shell' => $shell->id]);
        $shell_dropbox = ShellDropboxFacet::create(['id_shell' => $shell->id]);

        $audiolist = AudioList::create();
        $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);
        $audiolist_view = AudioListViewFacet::create(['id_list' => $audiolist->id]);

        $audiolist_edit->shells()->save($shell);
        $audiolist_view->shells()->save($shell);

        $view_array[] = [
            "type" => "ocap",
            "ocapType" => "AudioListView",
            "url" => "/api/audiolist/$audiolist_view->swiss_number"
        ];
        $edit_array[] = [
            "type" => "ocap",
            "ocapType" => "AudioListEdit",
            "url" => "/api/audiolist/$audiolist_edit->swiss_number/edit"
        ];

        $response = $this->get("/api/shell/$shell_user->swiss_number");
        $this->assertEquals(json_encode($view_array),
            json_encode(json_decode($response->getContent())->contents->audiolists_view));
        $this->assertEquals(json_encode($edit_array),
            json_encode(json_decode($response->getContent())->contents->audiolists_edit));
    }

    private function generateAudioListFacet()
    {
        $random_facet = random_int(0, 1);

        $audiolist = AudioList::create();
        $audiolist_view = AudioListViewFacet::create(['id_list' => $audiolist->id]);
        $audiolist_edit = AudioListEditFacet::create(['id_list' => $audiolist->id]);

        return ($random_facet) ? $audiolist_view : $audiolist_edit;
    }

    private function generateAudioListsJson($random, $dropbox = null)
    {
        $audiolist_array = [];

        for ($i = 0; $i < $random; $i++) {
            $facet = $this->generateAudioListFacet();
            $json_facet = [
                'ocapType' => '',
                'ocap' => '/api/audiolist/' . $facet->swiss_number,
                'dropbox' => $dropbox
            ];
            if ($facet instanceof AudioListViewFacet) {
                $json_facet['ocapType'] = 'AudioListView';
            } else
                $json_facet['ocapType'] = 'AudioListEdit';
            $audiolist_array[] = $json_facet;
        }
        return $audiolist_array;
    }

    /** @test */
    public function updateShell()
    {
        $this->limitTo(50)->forAll(Generator\nat())->then(function ($random) {
            $shell = Shell::create();
            $shell_user = ShellUserFacet::create(['id_shell' => $shell->id]);
            $shell_dropbox = ShellDropboxFacet::create(['id_shell' => $shell->id]);

            $audiolist_array["audiolists"] = $this->generateAudioListsJson($random);
            $response = $this->put("/api/shell/$shell_user->swiss_number", ["data" => $audiolist_array]);
            $mapped_view = array_map(function($audiolist) {
                if (!empty($audiolist) && $audiolist["ocapType"] == "AudioListView")
                    return [
                        'type' => 'ocap',
                        'ocapType' => $audiolist["ocapType"],
                        'url' => $audiolist["ocap"]
                    ];
            }, $audiolist_array["audiolists"]);
            $mapped_edit = array_map(function($audiolist) {
                if (!empty($audiolist) && $audiolist["ocapType"] == "AudioListEdit")
                    return [
                        'type' => 'ocap',
                        'ocapType' => $audiolist["ocapType"],
                        'url' => $audiolist["ocap"] . '/edit'
                    ];
            }, $audiolist_array["audiolists"]);
            $response->assertStatus(200);
            $this->assertEquals(json_encode($response->getData()->contents->audiolists_edit),
                json_encode(array_values(array_filter($mapped_edit))));
            $this->assertEquals(json_encode($response->getData()->contents->audiolists_view),
                json_encode(array_values(array_filter($mapped_view))));
        });
    }

    /** @test */
    public function sendShellDropboxMessage()
    {
        $this->limitTo(50)->forAll(Generator\nat())->then(function ($random) {
            $shell1 = Shell::create();
            $shell_user1 = ShellUserFacet::create(['id_shell' => $shell1->id]);
            $shell_dropbox1 = ShellDropboxFacet::create(['id_shell' => $shell1->id]);

            $shell2 = Shell::create();
            $shell_user2 = ShellUserFacet::create(['id_shell' => $shell2->id]);
            $shell_dropbox2 = ShellDropboxFacet::create(['id_shell' => $shell2->id]);

            $audiolist_array = $this->generateAudioListsJson($random + 1, "/api/shell/$shell_dropbox2->swiss_number");
            $response = $this->post("/api/shell/$shell_user1->swiss_number", ["data" => $audiolist_array]);
            $response->assertStatus(200);
        });
    }

    /** @test */
    public function sendBadShellDropboxMessage()
    {
        $this->limitTo(50)->forAll(Generator\string(), Generator\string())->then(function ($ocap_type, $ocap) {
            if ($ocap_type != "" && $ocap != "" && substr($ocap_type, -1) != "/" && substr($ocap, -1) != "/") {
                $shell1 = Shell::create();
                $shell_user1 = ShellUserFacet::create(['id_shell' => $shell1->id]);
                $shell_dropbox1 = ShellDropboxFacet::create(['id_shell' => $shell1->id]);

                $shell2 = Shell::create();
                $shell_user2 = ShellUserFacet::create(['id_shell' => $shell2->id]);
                $shell_dropbox2 = ShellDropboxFacet::create(['id_shell' => $shell2->id]);

                $msg = [
                    "ocapType" => $ocap_type,
                    "ocap" => $ocap,
                    "dropbox" => "/api/shell/$shell_dropbox2->swiss_number"
                ];
                $response = $this->post("/api/shell/$shell_user1->swiss_number", ["data" => [$msg]]);
                $response->assertStatus(400);
            } else
                $this->assertTrue(true);
        });
    }
}
