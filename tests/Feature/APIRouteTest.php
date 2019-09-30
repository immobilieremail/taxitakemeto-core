<?php

namespace Tests\Feature;

use App\Audio;
use App\AudioList;
use App\Shell;

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

        $response   = $this->post(route('audio.store'));
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
        $response   = $this->delete(route('audiolist.audio.destroy', [
            'audiolist' => \str_random(24),
            'audio' => \str_random(24)]));
        $response
            ->assertStatus(404);
    }

    /** @test */
    public function shell_entry_point()
    {
        $response   = $this->get(route('shell.create'));
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
    public function get_shell_user_facet()
    {
        $shellWithFacets    = factory(Shell::class)->create();
        $response           = $this->get(route('shell.show', ['shell' => $shellWithFacets->userFacet]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(4)
            ->assertJsonStructure([
                'type',
                'dropbox',
                'update',
                'contents' => [
                    'audiolists_view',
                    'audiolists_edit'
                ]
            ]);
    }

    /** @test */
    public function get_shell_user_facet_with_audiolist()
    {
        $audiolistWithFacets    = factory(AudioList::class)->create();
        $shellWithFacets        = factory(Shell::class)->create();

        $shellWithFacets->audioListEdits()->save($audiolistWithFacets->editFacet);
        $shellWithFacets->audioListViews()->save($audiolistWithFacets->viewFacet);
        $response               = $this->get(route('shell.show', ['shell' => $shellWithFacets->userFacet]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(4)
            ->assertJsonStructure([
                'type',
                'dropbox',
                'update',
                'contents' => [
                    'audiolists_view' => [
                        [
                            'type',
                            'ocapType',
                            'url'
                        ]
                    ],
                    'audiolists_edit' => [
                        [
                            'type',
                            'ocapType',
                            'url'
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function get_bad_shell_user_facet()
    {
        $response   = $this->get(route('shell.show', ['shell' => \str_random(24)]));
        $response
            ->assertStatus(404);
    }

    public function generate_audiolists_json($shell) : Array
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
            $audiolists_array        = $this->generate_audiolists_json($shellWithFacets);
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
    public function update_shell_with_bad_data_request()
    {
        $shellWithFacets    = factory(Shell::class)->create();
        $bad_request        = ["audiolists" => [["id" => "a"], ["id" => "b"]]];
        $response           = $this->put(route('shell.update', [$shellWithFacets->userFacet->swiss_number]), ["data" => $bad_request]);

        $response
            ->assertStatus(400);
    }

    /** @test */
    public function update_shell_with_bad_something_request()
    {
        $shellWithFacets    = factory(Shell::class)->create();
        $bad_request        = ["b" => [["id" => "a"], ["id" => "b"]]];
        $response           = $this->put(route('shell.update', [$shellWithFacets->userFacet->swiss_number]), ["a" => $bad_request]);

        $response
            ->assertStatus(400);
    }

    /** @test */
    public function update_non_existant_shell()
    {
        $random_number          = '4da7848daj';
        $bad_request            = ["audiolists" => [["id" => "a"], ["id" => "b"]]];
        $response               = $this->put(route('shell.update', [$random_number]), ["data" => $bad_request]);

        $response
            ->assertStatus(404);
    }
}
