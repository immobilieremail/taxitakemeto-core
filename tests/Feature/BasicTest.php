<?php

namespace Tests\Feature;

use App\Edit,
    App\View,
    App\Sound,
    App\SoundList,
    App\JoinListSound,
    Illuminate\Http\Request,
    App\Http\Controllers\IndexController;

use Eris\Generator,
    Eris\TestTrait;

use Tests\TestCase,
    Illuminate\Foundation\Testing\RefreshDatabase;

class BasicTest extends TestCase
{

    use RefreshDatabase;
    use TestTrait;

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
}
