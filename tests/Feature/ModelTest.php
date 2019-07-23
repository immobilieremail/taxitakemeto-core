<?php

namespace Tests\Feature;

use App\Edit,
    App\View,
    App\Sound,
    App\SoundList,
    App\JoinListSound,
    Illuminate\Http\Request;

use Tests\TestCase,
    Illuminate\Foundation\Testing\RefreshDatabase;

require_once __DIR__ . "/../../app/Http/Controllers/myfunctions/rand_nbr.php";

class ModelTest extends TestCase
{
    public function testGetFirstEdit()
    {
        $id_edit = rand_large_nbr();
        $id_view = rand_large_nbr();
        $match = [
            'id_edit' => $id_edit,
            'id_view' => $id_view
        ];

        $edit = Edit::create($match);
        $rtrn = Edit::getFirstEdit($id_edit);
        $this->assertEquals($id_edit, $rtrn->id_edit);
    }

    public function testGetViewNbr()
    {
        $id_edit = rand_large_nbr();
        $id_view = rand_large_nbr();
        $match = [
            'id_edit' => $id_edit,
            'id_view' => $id_view
        ];

        $edit = Edit::create($match);
        $view_nbr = Edit::getViewNbr($id_edit);
        $this->assertEquals($id_view, $view_nbr);
    }

    public function testGetFirstView()
    {
        $id_view = rand_large_nbr();
        $id_list = rand_large_nbr();
        $match = [
            'id_view' => $id_view,
            'id_list' => $id_list
        ];

        $soundlist = View::create($match);
        $rtrn = View::getFirstView($id_view);
        $this->assertEquals($id_view, $rtrn->id_view);
    }

    public function testGetSoundListNbr()
    {
        $id_view = rand_large_nbr();
        $id_list = rand_large_nbr();
        $match = [
            'id_view' => $id_view,
            'id_list' => $id_list
        ];

        $edit = View::create($match);
        $soundlist_nbr = View::getSoundListNbr($id_view);
        $this->assertEquals($id_list, $soundlist_nbr);
    }

    public function testGetFirstSoundList()
    {
        $id = rand_large_nbr();
        $match = ['id' => $id];

        $soundlist = SoundList::create($match);
        $rtrn = SoundList::getFirstSoundList($id);
        $this->assertEquals($id, $rtrn->id);
    }

    public function testJoinListSoundAddToDB()
    {
        $sound_nbr = rand_large_nbr();
        $soundlist_nbr = rand_large_nbr();

        $sound = Sound::create(['id' => $sound_nbr, 'path' => "/$sound_nbr.mp3"]);
        $soundlist = SoundList::create(['id' => $soundlist_nbr]);
        if (JoinListSound::addToDb($sound_nbr, $soundlist_nbr) == true) {
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

        if (Sound::addToDB($sound_nbr, "/$sound_nbr.mp3") == true) {
            $this->assertDatabaseHas('sounds', ['id' => $sound_nbr]);
        } else {
            $this->assertFalse(true);
        }
    }
}
