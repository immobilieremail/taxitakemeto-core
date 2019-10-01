<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Audio;
use App\AudioList;
use App\Shell;

use Eris\Generator,
    Eris\TestTrait;

class ErisTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    private function generate_audios_json() : Array
    {
        $random = rand(0, 10);
        $audio_array["audios"] = [];

        for ($i = 0; $i < $random; $i++) {
            $audioWithFacets = factory(Audio::class)->create();

            $audio_array["audios"][] = [
                'ocap' => route('audio.show', ['audio' => $audioWithFacets->viewFacet->swiss_number])
            ];
        }
        return $audio_array;
    }

    /** @test */
    public function update_audiolist()
    {
        $this->limitTo(50)->forAll(Generator\nat())->then(function () {
            $audiolistWithFacets    = factory(AudioList::class)->create();
            $audio_array            = $this->generate_audios_json();
            $response               = $this->put(route('audiolist.update', ['audiolist' => $audiolistWithFacets->editFacet->swiss_number]), ['data' => $audio_array]);

            $mapped_audio           = array_map(function ($audio) {
                return [
                    'type' => 'ocap',
                    'ocapType' => 'AudioView',
                    'url' => $audio["ocap"]
                ];
            }, $audio_array["audios"]);

            $response
                ->assertStatus(200)
                ->assertJsonCount(4)
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

    public function generate_audiolists_json() : Array
    {
        $random = rand(0, 10);
        $audiolist_array["audiolists"] = [];

        for ($i = 0; $i < $random; $i++) {
            $select_facet           = rand(0, 1);
            $audioListWithFacets    = factory(AudioList::class)->create();

            if ($select_facet == 0) {
                $audiolist_array["audiolists"][] = [
                    'ocapType' => 'AudioListView',
                    'ocap' => route('audiolist.show', ['audiolist' => $audioListWithFacets->viewFacet->swiss_number])
                ];
            } else {
                $audiolist_array["audiolists"][] = [
                    'ocapType' => 'AudioListEdit',
                    'ocap' => route('audiolist.edit', ['audiolist' => $audioListWithFacets->editFacet->swiss_number])
                ];
            }
        }
        return $audiolist_array;
    }

    /** @test */
    public function update_shell()
    {
        $this->limitTo(50)->forAll(Generator\nat())->then(function () {
            $shellWithFacets        = factory(Shell::class)->create();
            $audiolists_array       = $this->generate_audiolists_json();
            $response               = $this->put(route('shell.update', ['shell' => $shellWithFacets->userFacet->swiss_number]), ['data' => $audiolists_array]);

            $mapped_audiolists_view = array_filter(array_map(function ($audiolist) {
                if ($audiolist['ocapType'] == 'AudioListView') {
                    return [
                        'type' => 'ocap',
                        'ocapType' => $audiolist['ocapType'],
                        'url' => $audiolist['ocap']
                    ];
                } else
                    return null;
            }, $audiolists_array['audiolists']));

            $mapped_audiolists_edit = array_filter(array_map(function ($audiolist) {
                if ($audiolist['ocapType'] == 'AudioListEdit') {
                    return [
                        'type' => 'ocap',
                        'ocapType' => $audiolist['ocapType'],
                        'url' => $audiolist['ocap']
                    ];
                } else
                    return null;
            }, $audiolists_array['audiolists']));

            $response
                ->assertStatus(200)
                ->assertJsonCount(4)
                ->assertJsonStructure([
                    'type',
                    'dropbox',
                    'update',
                    'contents'
                ]);

            $this->assertEquals(json_encode($response->getData()->contents->audiolists_view),
                json_encode(array_values($mapped_audiolists_view)));
            $this->assertEquals(json_encode($response->getData()->contents->audiolists_edit),
                json_encode(array_values($mapped_audiolists_edit)));
        });
    }

    /** @test */
    public function send_shell()
    {
        $this->limitTo(50)->forAll(Generator\nat())->then(function ($rand) {
            $rand = ($rand < 1) ? 1 : $rand;

            $shell_receiver         = factory(Shell::class)->create();
            $request                = [];

            for ($i = 0; $i < $rand; $i++) {
                $audiolistWithFacets = factory(AudioList::class)->create();
                $request['data'][] = [
                    'ocapType' => 'AudioListView',
                    'ocap' => route('audiolist.show', ['audiolist' => $audiolistWithFacets->viewFacet->swiss_number])
                ];
            }

            $response   = $this->post(route('shell.send', ['shell' => $shell_receiver->dropboxFacet->swiss_number]), $request);
            $response
                ->assertStatus(200);
        });
    }
}
