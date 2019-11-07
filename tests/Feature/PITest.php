<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\OcapList;

use App\Models\PI;

class PITest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     */
    public function create_pi()
    {
        $ocaplist = factory(OcapList::class)->create();
        $response = $this->post(route('pi.store'), [
            'data' => [
                'title' => 'Title',
                'description' => 'Description',
                'medias' => route('obj.show', $ocaplist->editFacet->id)
            ]
        ]);

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
    public function view_pi()
    {
        $pi = factory(PI::class)->create();
        $response = $this->get(route('pi.show', ['obj' => $pi->viewFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                'type',
                'data' => [
                    'title',
                    'description',
                    'medias' => []
                ]
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_view_pi()
    {
        $response = $this->get(route('pi.show', ['obj' => 'something bad']));
        $response
            ->assertStatus(404);
    }

    /**
     * @test
     *
     */
    public function edit_pi()
    {
        $pi = factory(PI::class)->create();
        $response = $this->get(route('pi.show', ['obj' => $pi->editFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                'type',
                'view_facet',
                'data' => [
                    'title',
                    'description',
                    'medias' => []
                ]
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_edit_pi()
    {
        $response = $this->get(route('pi.show', ['obj' => 'something very bad']));
        $response
            ->assertStatus(404);
    }
}
