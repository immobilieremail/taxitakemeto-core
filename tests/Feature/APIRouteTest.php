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

use Eris\Generator,
    Eris\TestTrait;

class APIRouteTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    /** @test */
    public function get_audio_view()
    {
        $audioWithFacets    = factory(Audio::class)->create();

        $response = $this->get(route('audio.show', ['audio' => $audioWithFacets->viewFacet->swiss_number]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                'type',
                'path'
            ]);
    }

    /** @test */
    public function get_non_existant_audio_view()
    {
        $random_number = "089avg4w6";
        $response = $this->get(route('audio.show', ['audio' => $random_number]));
        $response
            ->assertStatus(404);
    }

    /** @test */
    public function get_audio_edit()
    {
        $audioWithFacets    = factory(Audio::class)->create();

        $response = $this->get(route('audio.edit', ['audio' => $audioWithFacets->editFacet->swiss_number]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(4)
            ->assertJsonStructure([
                'type',
                'view_facet',
                'path',
                'delete'
            ]);
    }

    /** @test */
    public function get_non_existant_audio_edit()
    {
        $random_number = "06745aha54";
        $response = $this->get(route('audio.edit', ['audio' => $random_number]));
        $response
            ->assertStatus(404);
    }

    /** @test */
    public function delete_audio()
    {
        $audioWithFacets    = factory(Audio::class)->create();

        $file = "public/storage/converts/$audioWithFacets->path";
        $handle = fopen($file, 'w');
        fclose($handle);

        $response = $this->delete(route('audio.destroy', ['audio' => $audioWithFacets->editFacet->swiss_number]));
        $response->assertStatus(200);
    }

    /** @test */
    public function delete_non_existant_audio_file()
    {
        $audioWithFacets    = factory(Audio::class)->create();

        $response = $this->delete(route('audio.destroy', ['audio' => $audioWithFacets->editFacet->swiss_number]));
        $response->assertStatus(404);
    }

    /** @test */
    public function delete_non_existant_audio_from_non_exisant_audio_facet()
    {
        $random_number = "ajc5a8pfb0";
        $response = $this->delete(route('audio.destroy', ['audio' => $random_number]));
        $response->assertStatus(404);
    }

    private function generate_audios_json($audiolist) : Array
    {
        $random = rand(0, 10);
        $audio_array["audios"] = [];

        for ($i = 0; $i < $random; $i++) {
            $audioWithFacets = factory(Audio::class)->create();

            $audiolist->audioViews()->save($audioWithFacets->viewFacet);
            $audiolist->audioEdits()->save($audioWithFacets->editFacet);
            $audio_array["audios"][] = [
                'ocap' => route('audio.show', ['audio' => $audioWithFacets->viewFacet->swiss_number])
            ];
        }
        return $audio_array;
    }

    /** @test */
    public function update_audiolist()
    {
        $this->limitTo(10)->forAll(Generator\nat())->then(function () {
            $audiolistWithFacets    = factory(AudioList::class)->create();
            $audio_array            = $this->generate_audios_json($audiolistWithFacets);
            $response               = $this->put(route('audiolist.update', [$audiolistWithFacets->editFacet->swiss_number]), ['data' => $audio_array]);

            $mapped_audio           = array_map(function ($audio) {
                return [
                    'type' => 'ocap',
                    'ocapType' => 'AudioView',
                    'url' => $audio["ocap"]
                ];
            }, $audio_array["audios"]);

            $response
                ->assertStatus(200)
                ->assertJsonStructure([
                    'type',
                    'update',
                    'view_facet',
                    'contents'
                ]);

            $this->assertEquals(json_encode($response->getData()->contents),
                json_encode($mapped_audio));
        });
    }

    /** @test */
    public function update_audiolist_with_bad_data_request()
    {
        $audiolistWithFacets    = factory(AudioList::class)->create();
        $bad_request            = ["audios" => [["id" => "a"], ["id" => "b"]]];
        $response               = $this->put(route('audiolist.update', [$audiolistWithFacets->editFacet->swiss_number]), ["data" => $bad_request]);

        $response
            ->assertStatus(400);
    }

    /** @test */
    public function update_audiolist_with_bad_something_request()
    {
        $audiolistWithFacets    = factory(AudioList::class)->create();
        $bad_request            = ["b" => [["id" => "a"], ["id" => "b"]]];
        $response               = $this->put(route('audiolist.update', [$audiolistWithFacets->editFacet->swiss_number]), ["a" => $bad_request]);

        $response
            ->assertStatus(400);
    }

    /** @test */
    public function update_non_existant_audiolist()
    {
        $random_number          = '4da7848daj';
        $bad_request            = ["audios" => [["id" => "a"], ["id" => "b"]]];
        $response               = $this->put(route('audiolist.update', [$random_number]), ["data" => $bad_request]);

        $response
            ->assertStatus(404);
    }

    /** @test */
    public function audiolist_entry_point()
    {
        $response       = $this->get(route('audiolist.create'));

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
        $audioWithFacets        = factory(Audio::class)->create();

        $audiolistWithFacets->audioViews()->save($audioWithFacets->viewFacet);
        $audiolistWithFacets->audioEdits()->save($audioWithFacets->editFacet);
        $response   = $this->get(route('audiolist.show', ['audiolist' => $audiolistWithFacets->viewFacet->swiss_number]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                'type',
                'contents' => [
                    [
                        'type',
                        'ocapType',
                        'url'
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
        $audioWithFacets        = factory(Audio::class)->create();

        $audiolistWithFacets->audioViews()->save($audioWithFacets->viewFacet);
        $audiolistWithFacets->audioEdits()->save($audioWithFacets->editFacet);
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
                        'type',
                        'ocapType',
                        'url'
                    ]
                ]
            ]);
    }

    /** @test */
    public function audio_entry_point_bad_audio()
    {
        $audiolistWithFacets    = factory(AudioList::class)->create();

        $response   = $this->post(route('audio.store'), ['audio' => '']);
        $response
            ->assertStatus(302);
    }

    /** @test */
    public function destroy_audio()
    {
        $audioWithFacets        = factory(Audio::class)->create();

        Storage::disk('converts')->put($audioWithFacets->path, '');

        $response   = $this->delete(route('audio.destroy', [
            'audio' => $audioWithFacets->editFacet->swiss_number]));
        $response
            ->assertStatus(200);
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
}
