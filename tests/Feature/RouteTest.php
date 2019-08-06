<?php

namespace Tests\Feature;

use App\Shell,
    App\Audio,
    App\AudioList,
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
        $audiolist = AudioList::create();
        $audiolist_edit_facet = AudioListEditFacet::create(['id_list' => $audiolist->id]);
        $response = $this->get("/en/audiolist_edit/$audiolist_edit_facet->swiss_number");

        $response->assertStatus(200);
    }

    public function testAccessView()
    {
        $audiolist = AudioList::create();
        $audiolist_view_facet = AudioListViewFacet::create(['id_list' => $audiolist->id]);
        $response = $this->get("/en/list-audio/$audiolist_view_facet->swiss_number");

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
        $rand_nbr = rand();
        $this->assertDatabaseMissing('audio_list_edit_facets', ['swiss_number' => $rand_nbr]);
        $response = $this->get("/en/audiolist_edit/$rand_nbr");
        $response->assertStatus(404);
    }

    public function testAccessNonExistantView()
    {
        $rand_nbr = rand();
        $this->assertDatabaseMissing('audio_list_view_facets', ['swiss_number' => $rand_nbr]);
        $response = $this->get("/en/list-audio/$rand_nbr");
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
        $shell = Shell::create();
        $audiolist = AudioList::create();
        $audiolist_edit_facet = AudioListEditFacet::create(['id_list' => $audiolist->id]);
        $response = $this->delete("/en/audiolist_edit/$audiolist_edit_facet->swiss_number/123");

        $response->assertStatus(404);
    }

    public function testDeleteSoundFromNonExistantEdit()
    {
        $audio = Audio::create(['path' => '/storage/uploads/', 'extension' => 'mp3']);
        $response = $this->delete("/en/audiolist_edit/123/$audio->swiss_number");

        $response->assertStatus(404);
    }
}