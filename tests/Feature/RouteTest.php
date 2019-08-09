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

use Illuminate\Support\Facades\Storage;

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

    public function testCreateShell()
    {
        $count_before = Shell::all()->count();
        $this->post("/en/shell");
        $count_after = Shell::all()->count();
        $this->assertEquals($count_before + 1, $count_after);
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
        $shell = Shell::create();
        $count_before = AudioListEditFacet::all()->count();
        $response = $this->post("/en/shell/$shell->swiss_number/new_audio_list");
        $count_after = AudioListEditFacet::all()->count();
        $this->assertEquals($count_before + 1, $count_after);
        $response->assertStatus(302);
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
        $audiolist = AudioList::create();
        $audiolist_edit_facet = AudioListEditFacet::create(['id_list' => $audiolist->id]);
        $audio = Audio::create(['extension' => 'mp3']);
        Storage::disk('converts')->put($audio->path, NULL);
        $response = $this->delete("/en/audiolist_edit/$audiolist_edit_facet->swiss_number/$audio->swiss_number");

        $response->assertStatus(303);
    }

    public function testDeleteNonExistantSound()
    {
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