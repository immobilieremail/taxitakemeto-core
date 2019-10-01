<?php

namespace Tests\Feature;

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
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Eris\Generator,
    Eris\TestTrait;

class ModelTest extends TestCase
{
    use TestTrait;
    use RefreshDatabase;

    /** @test */
    public function audio_multiple_add_and_delete()
    {
        $this->limitTo(10)->forAll(Generator\nat(), Generator\nat())->then(function ($nb1, $nb2) {
            $nbr_add = ($nb1 > $nb2) ? $nb1 : $nb2;
            $nbr_del = ($nb1 < $nb2) ? $nb1 : $nb2;
            $audio_id_array = [];

            $count_before = Audio::all()->count();
            for ($i = 0; $i < $nbr_add; $i++) {
                $audio_id_array[] = Audio::create(['extension' => 'mp3']);
            }
            for ($j = 0; $j < $nbr_del; $j++) {
                $audio_id_array[$j]->delete();
            }
            $count_after = Audio::all()->count();

            $this->assertEquals($count_after - $count_before, $nbr_add - $nbr_del);
        });
    }

    /** @test */
    public function access_audio_through_facet()
    {
        $audioWithFacets    = factory(Audio::class)->create();

        $this->assertEquals($audioWithFacets->id, $audioWithFacets->editFacet->audio->id);
        $this->assertEquals($audioWithFacets->path, $audioWithFacets->editFacet->audio->path);
        $this->assertEquals($audioWithFacets->id, $audioWithFacets->viewFacet->audio->id);
        $this->assertEquals($audioWithFacets->path, $audioWithFacets->viewFacet->audio->path);
    }

    /** @test */
    public function access_audiolist_through_audio_facet()
    {
        $audioWithFacets        = factory(Audio::class)->create();
        $audiolistWithFacets    = factory(AudioList::class)->create();

        $audiolistWithFacets->audioViews()->save($audioWithFacets->viewFacet);
        $audiolistWithFacets->audioEdits()->save($audioWithFacets->editFacet);

        $this->assertEquals($audiolistWithFacets->id, $audioWithFacets->editFacet->audiolists->first()->id);
        $this->assertEquals($audiolistWithFacets->id, $audioWithFacets->viewFacet->audiolists->first()->id);
    }

    /** @test */
    public function access_shell_through_audiolist_facet()
    {
        $audiolistWithFacets    = factory(AudioList::class)->create();
        $shellWithFacets        = factory(Shell::class)->create();

        $shellWithFacets->audioListViews()->save($audiolistWithFacets->viewFacet);
        $shellWithFacets->audioListEdits()->save($audiolistWithFacets->editFacet);

        $this->assertEquals($shellWithFacets->id, $audiolistWithFacets->editFacet->shells->first()->id);
        $this->assertEquals($shellWithFacets->id, $audiolistWithFacets->viewFacet->shells->first()->id);
    }

    /** @test */
    public function get_audio_edits()
    {
        $audioWithFacets        = factory(Audio::class)->create();
        $audiolistWithFacets    = factory(AudioList::class)->create();

        $audiolistWithFacets->audioEdits()->save($audioWithFacets->editFacet);
        $audio_edits = $audiolistWithFacets->getAudioEdits();
        $audio_edits_forged = [
            [
                'type' => 'ocap',
                'ocapType' => 'AudioEdit',
                'url' => route('audio.edit', ['audio' => $audioWithFacets->editFacet->swiss_number])
            ]
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($audio_edits), json_encode($audio_edits_forged));
    }
}
