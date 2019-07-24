<?php

namespace Tests\Feature;

use App\Edit,
    App\Sound,
    App\Http\Controllers\UploadAudioController;

use Tests\TestCase,
    Illuminate\Http\Request,
    Symfony\Component\HttpFoundation\File\UploadedFile;

class RouteTest extends TestCase
{
    public function testAccessUpload()
    {
        $gen = $this->post('/');
        $edit = Edit::all()->first();
        $response = $this->get("/upload-audio/$edit->id_edit");

        $response->assertStatus(200);
    }

    public function testAccessView()
    {
        $gen = $this->post('/');
        $edit = Edit::all()->first();
        $response = $this->get("/list-audio/$edit->id_view");

        $response->assertStatus(200);
    }

    public function testCreateList()
    {
        $count_before = Edit::all()->count();
        $response = $this->post('/');
        $count_after = Edit::all()->count();
        $this->assertEquals($count_before + 1, $count_after);
        $response->assertStatus(201);
    }
}