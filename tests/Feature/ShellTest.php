<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Shell;
use App\Models\OcapList;

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
            ->assertJsonCount(2)
            ->assertJsonStructure([
                'type',
                'data'
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
    public function update_shell_travel_list()
    {
        $shell = factory(Shell::class)->create();
        $ocaplist = factory(OcapList::class)->create();
        $request = [
            'travels' => route('obj.show', ['obj' => $ocaplist->editFacet])
        ];

        $response = $this->put(route('obj.update', ['obj' => $shell->userFacet]), $request);
        $response
            ->assertStatus(204);

        $this->assertEquals($ocaplist->editFacet->id, $shell->travelOcapListFacets->first()->id);
    }

    /**
     * @test
     *
     */
    public function bad_request_update_shell_travel_list()
    {
        $shell = factory(Shell::class)->create();
        $request = [
            'travels' => route('obj.show', ['obj' => 'saumon de Norvège'])
        ];

        $response = $this->put(route('obj.update', ['obj' => $shell->userFacet]), $request);
        $response
            ->assertStatus(400);
    }

    /**
     * @test
     *
     */
    public function bad_shell_update_shell_travel_list()
    {
        $request = [
            'travels' => route('obj.show', ['obj' => 'saumon de Norvège'])
        ];

        $response = $this->put(route('obj.update', ['obj' => 'truite du Pérou']), $request);
        $response
            ->assertStatus(404);
    }
}
