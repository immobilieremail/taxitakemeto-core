<?php

namespace Tests\Feature;

use App\Shell,
    App\Audio,
    App\AudioList,
    App\JoinListAudio,
    App\AudioListEditFacet,
    App\AudioListViewFacet,
    Illuminate\Http\Request,
    App\Http\Controllers\AudioListController;

use Eris\Generator,
    Eris\TestTrait;

use Tests\TestCase,
    Illuminate\Foundation\Testing\RefreshDatabase;

class ControllerTest extends TestCase
{
    use TestTrait;
    use RefreshDatabase;

    /** @test */
    public function basicTest()
    {
        $this->assertTrue(true);
    }

    /** test */
    public function shareViewToShell()
    {
        $shell_1 = Shell::create();
        $shell_2 = Shell::create();
        $audio_list = AudioList::create();
        $edit = AudioListEditFacet::create(['id_list' => $audio_list->id]);
        $request_param = [
            'share_to' => $shell_2_id,
            'view' => true,
            'edit' => false
        ];

        $controller = new AudioListController;
        $request = Request::create("/en/audiolist_edit/$edit_id/share", 'POST', $request_param);
        $controller->share($request, 'en', $edit_id);

        $new_view = AudioListViewFacet::where('id_shell', $shell_2_id)->first();
        $response = $this->get("/en/list-audio/$new_view->id");
        $response->assertStatus(200);
    }

    /** test */
    public function shareEditToShell()
    {
        $shell_1 = Shell::create();
        $shell_2 = Shell::create();
        $audio_list = AudioList::create();
        $edit = AudioListEditFacet::create(['id_list' => $audio_list->id]);
        $request_param = [
            'share_to' => $shell_2_id,
            'view' => false,
            'edit' => true
        ];

        $controller = new AudioListController;
        $request = Request::create("/en/audiolist_edit/$edit_id/share", 'POST', $request_param);
        $controller->share($request, 'en', $edit_id);

        $new_edit = AudioListEditFacet::where('id_shell', $shell_2_id)->first();
        $response = $this->get("/en/audiolist_edit/$new_edit->id");
        $response->assertStatus(200);
    }

    /** test */
    public function shareViewAndEditToShell()
    {
        $shell_1 = Shell::create();
        $shell_2 = Shell::create();
        $audio_list = AudioList::create();
        $edit = AudioListEditFacet::create(['id_list' => $audio_list->id]);
        $request_param = [
            'share_to' => $shell_2_id,
            'view' => true,
            'edit' => true
        ];

        $controller = new AudioListController;
        $request = Request::create("/en/audiolist_edit/$edit_id/share", 'POST', $request_param);
        $controller->share($request, 'en', $edit_id);

        $new_edit = AudioListEditFacet::where('id_shell', $shell_2_id)->first();
        $response = $this->get("/en/audiolist_edit/$new_edit->id");
        $response->assertStatus(200);

        $new_view = AudioListViewFacet::where('id_shell', $shell_2_id)->first();
        $response = $this->get("/en/list-audio/$new_view->id");
        $response->assertStatus(200);
    }
}
