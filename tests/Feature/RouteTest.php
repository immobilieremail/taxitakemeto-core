<?php

namespace Tests\Feature;

use App\Shell,
    App\Audio,
    App\AudioListEditFacet,
    App\AudioListViewFacet,
    App\Http\Controllers\UploadAudioController;

use Tests\TestCase;

use Eris\Generator,
    Eris\TestTrait;

use Illuminate\Http\Request,
    Symfony\Component\HttpFoundation\File\UploadedFile;

class RouteTest extends TestCase
{
    use TestTrait;

    public function testAccessIndex()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }

    public function testAccessEnIndex()
    {
        $response = $this->get('/en');

        $response->assertStatus(200);
    }

    public function testAccessFrIndex()
    {
        $response = $this->get('/fr');

        $response->assertStatus(200);
    }

    public function testAccessUpload()
    {
        if (Shell::all()->count() == 0) {
            $gen = $this->post('/en');
        }
        if (AudioListEditFacet::all()->count() == 0) {
            $shell = Shell::first();
            $this->post("/en/shell/$shell->id");
        }
        $edit = AudioListEditFacet::all()->first();
        $response = $this->get("/en/upload-audio/$edit->id");

        $response->assertStatus(200);
    }

    public function testAccessView()
    {
        if (Shell::all()->count() == 0) {
            $gen = $this->post('/en');
        }
        if (AudioListViewFacet::all()->count() == 0) {
            $shell = Shell::first();
            $this->post("/en/shell/$shell->id");
        }
        $view = AudioListViewFacet::all()->first();
        $response = $this->get("/en/list-audio/$view->id");

        $response->assertStatus(200);
    }

    public function testCreateList()
    {
        $count_before = AudioListEditFacet::all()->count();
        if (Shell::all()->count() == 0) {
            $gen = $this->post('/en');
        }
        $shell = Shell::first();
        $response = $this->post("/en/shell/$shell->id");
        $count_after = AudioListEditFacet::all()->count();
        $this->assertEquals($count_before + 1, $count_after);
        $response->assertStatus(303);
    }

    public function testAccessNonExistantUpload()
    {
        $edit_id = rand_large_nbr();
        $this->assertDatabaseMissing('audio_list_edit_facets', ['id' => $edit_id]);
        $response = $this->get("/en/upload-audio/$edit_id");
        $response->assertStatus(404);
    }

    public function testAccessNonExistantView()
    {
        $view_id = rand_large_nbr();
        $this->assertDatabaseMissing('audio_list_view_facets', ['id' => $view_id]);
        $response = $this->get("/en/list-audio/$view_id");
        $response->assertStatus(404);
    }

    public function testDeleteSound()
    {
        $audio_id = rand_large_nbr();

        if (Shell::all()->count() == 0) {
            $gen = $this->post('/en');
        }
        if (AudioListEditFacet::all()->count() == 0) {
            $shell = Shell::first();
            $this->post("/en/shell/$shell->id");
        }
        $edit = AudioListEditFacet::all()->first();
        $audio = Audio::addToDB($audio_id, "/$audio_id.mp3");
        $response = $this->delete("/en/upload-audio/$edit->id/$audio_id");

        $response->assertStatus(303);
    }

    public function testDeleteNonExistantSound()
    {
        $audio_id = rand_large_nbr();

        if (Shell::all()->count() == 0) {
            $gen = $this->post('/en');
        }
        if (AudioListEditFacet::all()->count() == 0) {
            $shell = Shell::first();
            $this->post("/en/shell/$shell->id");
        }
        $edit = AudioListEditFacet::all()->first();
        $response = $this->delete("/en/upload-audio/$edit->id_view/$audio_id");

        $response->assertStatus(404);
    }

    public function testDeleteSoundFromNonExistantEdit()
    {
        $audio_id = rand_large_nbr();
        $edit_id = rand_large_nbr();

        $audio = Audio::addToDB($audio_id, "/$audio_id.mp3");
        $response = $this->delete("/en/upload-audio/$edit_id/$audio->id");

        $response->assertStatus(404);
    }
}