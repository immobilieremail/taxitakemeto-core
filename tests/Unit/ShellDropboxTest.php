<?php

namespace Tests\Unit;

use App\ShellDropbox;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShellDropboxTest extends TestCase
{
    /** @test */
    public function routeCreateShellDropbox()
    {
        $count_before = ShellDropbox::all()->count();
        $this->post("/en/shell");
        $count_after = ShellDropbox::all()->count();
        $this->assertEquals($count_before + 1, $count_after);
    }
}