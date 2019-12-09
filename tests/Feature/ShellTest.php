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
    public function create_shell()
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
        $response = $this->get(route('obj.show', ['obj' => $shellWithFacets->userFacet->id]));
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

    /**
     * @test
     *
     */
    public function delete_shell()
    {
        $shellWithFacets = factory(Shell::class)->create();
        $response = $this->delete(route('obj.destroy', ['obj' => $shellWithFacets->userFacet->id]));
        $response
            ->assertStatus(204);
    }

    /**
     * @test
     *
     */
    public function bad_delete_shell()
    {
        $response = $this->delete(route('obj.destroy', ['obj' => 'this is really delicious']));
        $response
            ->assertStatus(404);
    }
}
