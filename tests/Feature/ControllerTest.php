<?php

namespace Tests\Feature;

use App\Shell,
    App\Audio,
    App\AudioList,
    App\JoinListAudio,
    App\AudioListEditFacet,
    App\AudioListViewFacet,
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
        $count_sounds_after = 0;
        $count_sounds_before = 0;

        $this->post('/en');
        $shell = Shell::first();
        $response = $this->post("/en/shell/$shell->id");
        $edit = AudioListEditFacet::first();
        $sound_id = rand_large_nbr();
        $param = [
            'id' => $sound_id,
            'path' => "/storage/uploads/$sound_id.mp3",
        ];

        $sound = Audio::create($param);

        $count_sounds_before = Audio::all()->count();

        $controller = new UploadAudioController;
        $request = Request::create("/en/upload-audio/$edit->id/$sound_id", 'DELETE', ['audio_path' => "/storage/uploads/$sound_id.mp3"]);
        $controller->destroy($request, 'en', $edit->id, $sound_id);

        $count_sounds_after = Audio::all()->count();

        $this->assertEquals($count_sounds_before - 1, $count_sounds_after, "With $count_sounds_before and $count_sounds_after");
        $this->assertDatabaseMissing('audio', $param);
    }

    public function testDeleteNonExistantSound()
    {
        $edit = NULL;
        $count_sounds_after = 0;
        $count_sounds_before = 0;

        $this->post('/en');
        $shell = Shell::first();
        $this->post("/en/shell/$shell->id");
        $edit = AudioListEditFacet::all()->first();

        $count_sounds_before = Audio::all()->count();

        $controller = new UploadAudioController;
        $request = Request::create("/en/upload-audio/$edit->id/-gef�z6816#�1hey", 'DELETE', ['audio_path' => '/storage/uploads/-gef�z6816#�1hey.mp3']);
        $controller->destroy($request, 'en', $edit->id, '-gef�z6816#�1hey');

        $count_sounds_after = Audio::all()->count();

        $this->assertEquals($count_sounds_before, $count_sounds_after);
    }
}
