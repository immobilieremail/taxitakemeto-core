<?php

namespace Tests\Feature;

use App\Edit,
    App\View,
    App\Sound,
    App\SoundList,
    App\JoinListSound,
    Illuminate\Http\Request;

use Eris\Generator,
    Eris\TestTrait;

use Tests\TestCase,
    Illuminate\Foundation\Testing\RefreshDatabase;

require_once __DIR__ . "/../../app/Http/Controllers/myfunctions/rand_nbr.php";

class ModelTest extends TestCase
{
    use TestTrait;

    public function testJoinListSoundAddToDB()
    {
        $sound_nbr = rand_large_nbr();
        $soundlist_nbr = rand_large_nbr();
        $sound = Sound::create(['id' => $sound_nbr, 'path' => "/$sound_nbr.mp3"]);
        $soundlist = SoundList::create(['id' => $soundlist_nbr]);

        if (JoinListSound::addToDb($sound_nbr, $soundlist_nbr) !== null) {
            $this->assertDatabaseHas('join_list_sounds', [
                'id_list' => $soundlist_nbr,
                'id_sound' => $sound_nbr
            ]);
        } else {
            $this->assertFalse(true);
        }
    }

    public function testSoundAddToDB()
    {
        $sound_nbr = rand_large_nbr();

        if (Sound::addToDB($sound_nbr, "/$sound_nbr.mp3") !== null) {
            $this->assertDatabaseHas('sounds', ['id' => $sound_nbr]);
        } else {
            $this->assertFalse(true);
        }
    }

    public function testSoundDeleteFromDB()
    {
        $sound_nbr = rand_large_nbr();

        $sound = Sound::addToDB($sound_nbr, "/$sound_nbr.mp3");
        if (Sound::deleteFromDB($sound_nbr)) {
            $this->assertDatabaseMissing('sounds', ['id' => $sound_nbr]);
        } else {
            $this->assertFalse(true);
        }
    }

    public function testJoinListSoundDeleteWithSound()
    {
        $sound_id = rand_large_nbr();

        $count_before = JoinListSound::all()->count();
        $soundlist = SoundList::create();
        $sound = Sound::addToDB($sound_id, "$sound_id.mp3");
        $joinlstsnd = JoinListSound::addToDB($sound_id, $soundlist->id);

        $match = ['id_list' => $soundlist->id, 'id_sound' => $sound_id];
        $this->assertDatabaseHas('join_list_sounds', $match);

        Sound::find($sound_id)->delete();
        $count_after = JoinListSound::all()->count();
        $this->assertEquals($count_before, $count_after);
    }

    public function testSoundMultipleAddAndDelete()
    {
        $this->limitTo(50)->forAll(Generator\nat(), Generator\nat())->then(function ($nb1, $nb2) {
            $nbr_add = ($nb1 > $nb2) ? $nb1 : $nb2;
            $nbr_del = ($nb1 < $nb2) ? $nb1 : $nb2;
            $sound_id_array = array();

            $count_before = Sound::all()->count();
            for ($i = 0; $i < $nbr_add; $i++) {
                $sound_nbr = rand_large_nbr();
                $sound = Sound::addToDB($sound_nbr, "/$sound_nbr.mp3");
                array_push($sound_id_array, $sound_nbr);
            }
            for ($j = 0; $j < $nbr_del; $j++) {
                Sound::deleteFromDB($sound_id_array[$j]);
            }
            $count_after = Sound::all()->count();

            $this->assertEquals($count_after - $count_before, $nbr_add - $nbr_del, "With $count_after - $count_before and $nbr_add - $nbr_del");
        });
    }
}
