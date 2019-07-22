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

class BasicTest extends TestCase
{

    use RefreshDatabase;
    use TestTrait;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertViewIs('index');
    }

    public function testCreateList()
    {
        $gen = $this->post('/');
        $edit = Edit::all()->first();
        $match = [
            'id_edit' => $edit->id_edit,
            'id_view' => $edit->id_view,
        ];

        $this->assertDatabaseHas('edits', $match);
    }

    public function testAccessUpload()
    {
        $gen = $this->post('/');
        $edit = Edit::all()->first();
        $response = $this->get("/upload-audio/$edit->id_edit");

        $response->assertViewIs('upload-audio');
    }

    public function testAccessView()
    {
        $gen = $this->post('/');
        $edit = Edit::all()->first();
        $response = $this->get("/list-audio/$edit->id_view");

        $response->assertViewIs('list-audio');
    }

    public function testAccessNonExistantUpload()
    {
        $this->forAll(Generator\string())->then(function ($string) {
            $response = $this->get("/upload-audio/$string");
            $response->assertStatus(200, "It was tested with $string.");
        });
    }

    public function testAccessNonExistantList()
    {
        $this->forAll(Generator\string())->then(function ($string) {
            $response = $this->get("/list-audio/$string");
            $response->assertStatus(200, "It was tested with $string.");
        });
    }

    public function testDeleteSound()
    {
        $gen = $this->post('/');
        $edit = Edit::all()->first();
        $view = View::all()->first();
        $match = [
            'id' => 123456789,
            'path' => '/storage/uploads/123456789.mp3',
        ];

        $sound = Sound::create($match);

        $response = $this->delete("/upload-audio/$edit->id_edit/123456789");

        $this->assertDatabaseMissing('sounds', $match);
    }

    public function testDeleteNonExistantSound()
    {
        $gen = $this->post('/');
        $edit = Edit::all()->first();
        $response = $this->delete("/upload-audio/$edit->id_edit/-gef�z6816#�1hey");

        $response->assertStatus(302);
    }

    public function testCreateAndDeleteList()
    {
        $gen = $this->post('/');
        $edit = Edit::all()->first();
        $list = SoundList::all()->first();
        $destroy = SoundList::findOrFail($list->id)->delete();

        $response = $this->get("/upload-audio/$edit->id_edit");

        $response->assertStatus(200);
    }
}
