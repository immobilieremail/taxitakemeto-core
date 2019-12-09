<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Shell;

class ShellTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     */
    public function shell_entry_point()
    {
        $response = $this->post(route('shell.store'));
        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                'type',
                'ocapType',
                'url'
            ]);
    }

    /**
     * @test
     *
     */
    public function get_shell_user_facet()
    {
        $shellWithFacets = factory(Shell::class)->create();
        $response = $this->get(route('obj.show', ['obj' => $shellWithFacets->userFacet]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(4)
            ->assertJsonStructure([
                'type',
                'dropbox',
                'contents'
            ]);
    }

    /**
     * @test
     *
     */
    public function get_bad_shell_user_facet()
    {
        $response = $this->get(route('obj.show', ['obj' => 'çafaitallusion']));
        $response
            ->assertStatus(404);
    }
}
