<?php

namespace Tests\Feature;

use App\Edit,
    App\Sound,
    App\Http\Controllers\UploadAudioController;

use Tests\TestCase;

use Illuminate\Http\Request,
    Symfony\Component\HttpFoundation\File\UploadedFile;

class RouteTest extends TestCase
{
    public function testAccessUpload()
    {
        if (Edit::all()->count() == 0)
            $gen = $this->post('/en');
        $edit = Edit::all()->first();
        $response = $this->get("/en/upload-audio/$edit->id_edit");

        $response->assertStatus(200);
    }

    public function testAccessView()
    {
        if (Edit::all()->count() == 0)
            $gen = $this->post('/en');
        $edit = Edit::all()->first();
        $response = $this->get("/en/list-audio/$edit->id_view");

        $response->assertStatus(200);
    }

    public function testCreateList()
    {
        $count_before = Edit::all()->count();
        $response = $this->post('/en');
        $count_after = Edit::all()->count();
        $this->assertEquals($count_before + 1, $count_after);
        $response->assertStatus(303);
    }

    public function testAccessNonExistantUpload()
    {
        $edit_id = rand_large_nbr();
        $this->assertDatabaseMissing('edits', ['id_edit' => $edit_id]);
        $response = $this->get("/en/upload-audio/$edit_id");
        $response->assertStatus(404);
    }

    public function testAccessNonExistantView()
    {
        $view_id = rand_large_nbr();
        $this->assertDatabaseMissing('views', ['id_view' => $view_id]);
        $response = $this->get("/en/list-audio/$view_id");
        $response->assertStatus(404);
    }

    public function testDeleteSound()
    {
        $sound_id = rand_large_nbr();

        if (Edit::all()->count() == 0)
            $gen = $this->post('/en');
        $edit = Edit::all()->first();
        $sound = Sound::addToDB($sound_id, "/$sound_id.mp3");
        $response = $this->delete("/en/upload-audio/$edit->id_view/$sound_id");

        $response->assertStatus(303);
    }

    public function testDeleteNonExistantSound()
    {
        $sound_id = rand_large_nbr();

        if (Edit::all()->count() == 0)
            $gen = $this->post('/en');
        $edit = Edit::all()->first();
        $response = $this->delete("/en/upload-audio/$edit->id_view/$sound_id");

        $response->assertStatus(404);
    }

    public function testDeleteSoundFromNonExistantEdit()
    {
        $sound_id = rand_large_nbr();
        $edit_id = rand_large_nbr();

        $sound = Sound::addToDB($sound_id, "/$sound_id.mp3");
        $response = $this->delete("/en/upload-audio/$edit_id/$sound->id");

        $response->assertStatus(404);
    }
}