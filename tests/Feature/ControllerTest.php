<?php

namespace Tests\Feature;

use App\Edit,
    App\View,
    App\Sound,
    App\SoundList,
    App\JoinListSound,
    Illuminate\Http\Request,
    App\Http\Controllers\UploadAudioController;

use Eris\Generator,
    Eris\TestTrait;

use Tests\TestCase,
    Illuminate\Foundation\Testing\RefreshDatabase;

class ControllerTest extends TestCase
{
    use TestTrait;
    use RefreshDatabase;

    public function testDeleteSound()
    {
        $gen = $this->post('/');
        $edit = Edit::all()->first();
        $view = View::all()->first();
        $param = [
            'id' => 123456789,
            'path' => '/storage/uploads/123456789.mp3',
        ];

        $sound = Sound::create($param);

        $response = $this->delete("/upload-audio/$edit->id_edit/123456789");

        $this->assertDatabaseMissing('sounds', $param);
    }

    public function testDeleteNonExistantSound()
    {
        $edit = NULL;
        $count_sounds_after = 0;
        $count_sounds_before = 0;

        $gen = $this->post('/');
        $edit = Edit::all()->first();

        $sounds_before = Sound::all();
        foreach ($sounds_before as $sound_before) {
            if ($sound == NULL)
                $sound = $sound_before;
            $count_sounds_before += 1;
        }

        $controller = new UploadAudioController;
        $request = Request::create("/upload-audio/$edit->id_edit/-gef�z6816#�1hey", 'DELETE', ['audio_path' => '/storage/uploads/-gef�z6816#�1hey.mp3']);
        $controller->destroy($request, $edit->id_edit, '-gef�z6816#�1hey');

        $count_sounds_after = Sound::all()->count();

        $this->assertEquals($count_sounds_before, $count_sounds_after);
    }

    public function testCreateEdit()
    {
        $this->limitTo(100)->forAll(Generator\nat())->then(function ($nbr) {
            $count_edits_after = 0;
            $count_edits_before = 0;

            $edits_before = Edit::all();
            foreach ($edits_before as $edit_before)
                $count_edits_before += 1;

            for ($i = 1; $i <= $nbr; $i++) {
                $param = ['id_edit' => rand_large_nbr(), 'id_view' => rand_large_nbr()];
                Edit::create($param);
                $this->assertDatabaseHas('edits', $param);
            }
            $edits_after = Edit::all();
            foreach ($edits_after as $edit_after)
                $count_edits_after += 1;

            $this->assertEquals($count_edits_before + $nbr, $count_edits_after, "With $nbr.");
        });
    }

    public function testCreateList()
    {
        $this->limitTo(100)->forAll(Generator\nat())->then(function ($nbr) {
            $count_soundlists_after = 0;
            $count_soundlists_before = 0;

            $count_soundlists_before = SoundList::all()->count();

            for ($i = 1; $i <= $nbr; $i++) {
                $param = ['id' => rand_large_nbr()];
                SoundList::create($param);
                $this->assertDatabaseHas('sound_lists', $param);
            }
            $count_soundlists_after = SoundList::all()->count();

            $this->assertEquals($count_soundlists_before + $nbr, $count_soundlists_after, "With $nbr.");
        });
    }

    public function testDeleteList()
    {
        $this->limitTo(100)->forAll(Generator\nat())->then(function ($nbr) {
            $count_soundlists_after = 0;
            $count_soundlists_during = 0;
            $count_soundlists_before = 0;

            $count_soundlists_before = SoundList::all()->count();

            for ($i = 1; $i <= $nbr; $i++)
                SoundList::create(['id' => $i]);
            $count_soundlists_during = SoundList::all()->count();

            $this->assertEquals($count_soundlists_before + $nbr, $count_soundlists_during, "With $nbr.");

            for ($j = 1; $j <= $nbr; $j++) {
                SoundList::find($j)->delete();
                $this->assertDatabaseMissing('sound_lists', ['id' => $j]);
            }
            $count_soundlists_after = SoundList::all()->count();

            $this->assertEquals($count_soundlists_before, $count_soundlists_after, "With $nbr.");
        });
    }
}
