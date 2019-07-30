<?php

namespace Tests\Feature;

use App\Shell,
    App\Audio,
    App\AudioList,
    App\JoinListAudio,
    App\AudioListViewFacet,
    App\AudioListEditFacet,
    Illuminate\Http\Request;

use Eris\Generator,
    Eris\TestTrait;

use Tests\TestCase,
    Illuminate\Foundation\Testing\RefreshDatabase;

require_once __DIR__ . "/../../app/Http/Controllers/myfunctions/rand_nbr.php";

class ModelTest extends TestCase
{
    use TestTrait;

    public function testJoinListAudioAddToDB()
    {
        $audio_nbr = rand_large_nbr();
        $audiolist_nbr = rand_large_nbr();
        $audio = Audio::create(['id' => $audio_nbr, 'path' => "/$audio_nbr.mp3"]);
        $audiolist = AudioList::create(['id' => $audiolist_nbr]);

        if (JoinListAudio::addToDb($audio_nbr, $audiolist_nbr) !== null) {
            $this->assertDatabaseHas('join_list_audio', [
                'id_list' => $audiolist_nbr,
                'id_audio' => $audio_nbr
            ]);
        } else {
            $this->assertFalse(true);
        }
    }

    public function testAudioAddToDB()
    {
        $audio_nbr = rand_large_nbr();

        if (Audio::addToDB($audio_nbr, "/$audio_nbr.mp3") !== null) {
            $this->assertDatabaseHas('audio', ['id' => $audio_nbr]);
        } else {
            $this->assertFalse(true);
        }
    }

    public function testAudioDeleteFromDB()
    {
        $audio_nbr = rand_large_nbr();

        $audio = Audio::addToDB($audio_nbr, "/$audio_nbr.mp3");
        if (Audio::deleteFromDB($audio_nbr)) {
            $this->assertDatabaseMissing('audio', ['id' => $audio_nbr]);
        } else {
            $this->assertFalse(true);
        }
    }

    public function testJoinListAudioDeleteWithAudio()
    {
        $audio_id = rand_large_nbr();

        $count_before = JoinListAudio::all()->count();
        $audiolist = AudioList::create();
        $audio = Audio::addToDB($audio_id, "$audio_id.mp3");
        $joinlstsnd = JoinListAudio::addToDB($audio_id, $audiolist->id);

        $match = ['id_list' => $audiolist->id, 'id_audio' => $audio_id];
        $this->assertDatabaseHas('join_list_audio', $match);

        Audio::find($audio_id)->delete();
        $count_after = JoinListAudio::all()->count();
        $this->assertEquals($count_before, $count_after);
    }

    public function testAudioMultipleAddAndDelete()
    {
        $this->limitTo(10)->forAll(Generator\nat(), Generator\nat())->then(function ($nb1, $nb2) {
            $nbr_add = ($nb1 > $nb2) ? $nb1 : $nb2;
            $nbr_del = ($nb1 < $nb2) ? $nb1 : $nb2;
            $audio_id_array = array();

            $count_before = Audio::all()->count();
            for ($i = 0; $i < $nbr_add; $i++) {
                $audio_nbr = rand_large_nbr();
                $audio = Audio::addToDB($audio_nbr, "/$audio_nbr.mp3");
                array_push($audio_id_array, $audio_nbr);
            }
            for ($j = 0; $j < $nbr_del; $j++) {
                Audio::deleteFromDB($audio_id_array[$j]);
            }
            $count_after = Audio::all()->count();

            $this->assertEquals($count_after - $count_before, $nbr_add - $nbr_del, "With $count_after - $count_before and $nbr_add - $nbr_del");
        });
    }

    public function testAudioListViewFacetAddToDB()
    {
        if (Shell::find(1) == NULL)
            Shell::create(['id' => 1]);
        if (AudioList::find(1) == NULL)
            AudioList::create(['id' => 1]);
        $this->limitTo(10)->forAll(Generator\nat())->then(function ($nbr) {
            $count_before = AudioListViewFacet::all()->count();
            for ($i = 0; $i < $nbr; $i++) {
                $id = rand_large_nbr();
                AudioListViewFacet::addToDB($id, 1, 1);
            }
            $count_after = AudioListViewFacet::all()->count();
            $this->assertEquals($count_before + $nbr, $count_after, "With $count_before + $nbr and $count_after");
        });
    }

    public function testAudioListEditFacetAddToDB()
    {
        if (Shell::find(1) == NULL)
            Shell::create(['id' => 1]);
        if (AudioList::find(1) == NULL)
            AudioList::create(['id' => 1]);
        $this->limitTo(10)->forAll(Generator\nat())->then(function ($nbr) {
            $count_before = AudioListEditFacet::all()->count();
            for ($i = 0; $i < $nbr; $i++) {
                $id = rand_large_nbr();
                AudioListEditFacet::addToDB($id, 1, 1);
            }
            $count_after = AudioListEditFacet::all()->count();
            $this->assertEquals($count_before + $nbr, $count_after, "With $count_before + $nbr and $count_after");
        });
    }
}
