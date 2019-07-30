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

    public function testShareViewToShell()
    {
        $audio_list = AudioList::create();
        $shell_1_id = rand_large_nbr();
        $shell_1 = Shell::create(['id' => $shell_1_id]);
        $shell_2_id = rand_large_nbr();
        $shell_2 = Shell::create(['id' => $shell_2_id]);
        $edit_id = rand_large_nbr();
        $edit = AudioListEditFacet::addToDB($edit_id, $audio_list->id, $shell_1_id);
        $request_param = [
            'share_to' => $shell_2_id,
            'view' => true,
            'edit' => false
        ];

        $edit = AudioListEditFacet::find($edit_id);
        $controller = new UploadAudioController;
        $request = Request::create("/en/upload-audio/$edit_id/share", 'POST', $request_param);
        $controller->share($request, 'en', $edit_id);

        $new_view = AudioListViewFacet::where('id_shell', $shell_2_id)->first();
        $response = $this->get("/en/list-audio/$new_view->id");
        $response->assertStatus(200);
    }

    public function testShareEditToShell()
    {
        $audio_list = AudioList::create();
        $shell_1_id = rand_large_nbr();
        $shell_1 = Shell::create(['id' => $shell_1_id]);
        $shell_2_id = rand_large_nbr();
        $shell_2 = Shell::create(['id' => $shell_2_id]);
        $edit_id = rand_large_nbr();
        $edit = AudioListEditFacet::addToDB($edit_id, $audio_list->id, $shell_1_id);
        $request_param = [
            'share_to' => $shell_2_id,
            'view' => false,
            'edit' => true
        ];

        $edit = AudioListEditFacet::find($edit_id);
        $controller = new UploadAudioController;
        $request = Request::create("/en/upload-audio/$edit_id/share", 'POST', $request_param);
        $controller->share($request, 'en', $edit_id);

        $new_edit = AudioListEditFacet::where('id_shell', $shell_2_id)->first();
        $response = $this->get("/en/upload-audio/$new_edit->id");
        $response->assertStatus(200);
    }

    public function testShareViewAndEditToShell()
    {
        $audio_list = AudioList::create();
        $shell_1_id = rand_large_nbr();
        $shell_1 = Shell::create(['id' => $shell_1_id]);
        $shell_2_id = rand_large_nbr();
        $shell_2 = Shell::create(['id' => $shell_2_id]);
        $edit_id = rand_large_nbr();
        $edit = AudioListEditFacet::addToDB($edit_id, $audio_list->id, $shell_1_id);
        $request_param = [
            'share_to' => $shell_2_id,
            'view' => true,
            'edit' => true
        ];

        $edit = AudioListEditFacet::find($edit_id);
        $controller = new UploadAudioController;
        $request = Request::create("/en/upload-audio/$edit_id/share", 'POST', $request_param);
        $controller->share($request, 'en', $edit_id);

        $new_edit = AudioListEditFacet::where('id_shell', $shell_2_id)->first();
        $response = $this->get("/en/upload-audio/$new_edit->id");
        $response->assertStatus(200);

        $new_view = AudioListViewFacet::where('id_shell', $shell_2_id)->first();
        $response = $this->get("/en/list-audio/$new_view->id");
        $response->assertStatus(200);
    }
}
