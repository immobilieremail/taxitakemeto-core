<?php

namespace Tests\Feature;

use App\Shell,
    App\Audio,
    App\AudioList,
    App\JoinListAudio,
    App\JoinShellEdit,
    App\JoinShellView,
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
        $gen = $this->post('/en');
        $count_sounds_after = 0;
        $count_sounds_before = 0;
        $edit = AudioListEditFacet::all()->first();
        $sound_id = rand_large_nbr();
        $param = [
            'id' => $sound_id,
            'path' => "/storage/uploads/$sound_id.mp3",
        ];

        $sound = Audio::create($param);

        $count_sounds_before = Audio::all()->count();

        $controller = new UploadAudioController;
        $request = Request::create("/en/upload-audio/$edit->id_edit/$sound_id", 'DELETE', ['audio_path' => "/storage/uploads/$sound_id.mp3"]);
        $controller->destroy($request, 'en', $edit->id_edit, $sound_id);

        $count_sounds_after = Audio::all()->count();

        $this->assertEquals($count_sounds_before - 1, $count_sounds_after, "With $count_sounds_before and $count_sounds_after");
        $this->assertDatabaseMissing('audio', $param);
    }

    public function testDeleteNonExistantSound()
    {
        $edit = NULL;
        $count_sounds_after = 0;
        $count_sounds_before = 0;

        $gen = $this->post('/en');
        $edit = AudioListEditFacet::all()->first();

        $count_sounds_before = Audio::all()->count();

        $controller = new UploadAudioController;
        $request = Request::create("/en/upload-audio/$edit->id_edit/-gef�z6816#�1hey", 'DELETE', ['audio_path' => '/storage/uploads/-gef�z6816#�1hey.mp3']);
        $controller->destroy($request, 'en', $edit->id_edit, '-gef�z6816#�1hey');

        $count_sounds_after = Audio::all()->count();

        $this->assertEquals($count_sounds_before, $count_sounds_after);
    }

    public function testIfAllIsCreated()
    {
        $this->limitTo(100)->forAll(Generator\nat())->then(function ($nbr) {
            $count_edits_before = AudioListEditFacet::all()->count();
            $count_views_before = AudioListViewFacet::all()->count();
            $count_soundlists_before = AudioList::all()->count();
            $count_shells_before = Shell::all()->count();
            $count_joinshllviews_before = JoinShellView::all()->count();
            $count_joinshlledits_before = JoinShellEdit::all()->count();

            for ($i = 0; $i < $nbr; $i++)
                $this->post('/en');

            $count_edits_after = AudioListEditFacet::all()->count();
            $count_views_after = AudioListViewFacet::all()->count();
            $count_soundlists_after = AudioList::all()->count();
            $count_shells_after = Shell::all()->count();
            $count_joinshllviews_after = JoinShellView::all()->count();
            $count_joinshlledits_after = JoinShellEdit::all()->count();

            $this->assertEquals($count_edits_before + $nbr, $count_edits_after, "With $nbr");
            $this->assertEquals($count_views_before + $nbr, $count_views_after, "With $nbr");
            $this->assertEquals($count_soundlists_before + $nbr, $count_soundlists_after, "With $nbr");
            $this->assertEquals($count_shells_before + $nbr, $count_shells_after, "With $nbr");
            $this->assertEquals($count_joinshllviews_before + $nbr, $count_joinshllviews_after, "With $nbr");
            $this->assertEquals($count_joinshlledits_before + $nbr, $count_joinshlledits_after, "With $nbr");
        });
    }
}
