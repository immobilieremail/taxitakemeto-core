<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\OcapList;

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
}
