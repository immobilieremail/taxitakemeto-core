<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\OcapList;

use App\Models\Travel;

class TravelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     */
    public function create_travel()
    {
        $response = $this->post(route('travel.store'), [
            'title' => 'Title'
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
    public function create_travel_with_pis()
    {
        $ocaplist = factory(OcapList::class)->create();
        $response = $this->post(route('travel.store'), [
            'title' => 'Title',
            'pis' => route('obj.show', ['obj' => $ocaplist->viewFacet->id])
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
    public function view_travel()
    {
        $travel = factory(Travel::class)->create();
        $response = $this->get(route('obj.show', ['obj' => $travel->viewFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                'type',
                'data' => [
                    'title',
                    'pis'
                ]
            ]);
    }

    /**
     * @test
     *
     */
    public function view_travel_with_pis()
    {
        $travel = factory(Travel::class)->create();
        $ocaplist = factory(OcapList::class)->create();

        $travel->piOcapListFacets()->save($ocaplist->viewFacet);
        $response = $this->get(route('obj.show', ['obj' => $travel->viewFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                'type',
                'data' => [
                    'title',
                    'pis'
                ]
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_view_travel()
    {
        $response = $this->get(route('obj.show', ['obj' => 'something bad']));
        $response
            ->assertStatus(404);
    }

    /**
     * @test
     *
     */
    public function edit_travel()
    {
        $travel = factory(Travel::class)->create();
        $response = $this->get(route('obj.show', ['obj' => $travel->editFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                'type',
                'view_facet',
                'data' => [
                    'title',
                    'pis'
                ]
            ]);
    }

    /**
     * @test
     *
     */
    public function edit_travel_with_pis()
    {
        $travel = factory(Travel::class)->create();
        $ocaplist = factory(OcapList::class)->create();

        $travel->piOcapListFacets()->save($ocaplist->viewFacet);
        $response = $this->get(route('obj.show', ['obj' => $travel->editFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                'type',
                'view_facet',
                'data' => [
                    'title',
                    'pis'
                ]
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_edit_travel()
    {
        $response = $this->get(route('obj.show', ['obj' => 'something very bad']));
        $response
            ->assertStatus(404);
    }

    /**
     * @test
     *
     */
    public function update_travel()
    {
        $travel = factory(Travel::class)->create();
        $request = [
            'title' => 'Titre'
        ];
        $response = $this->put(route('obj.update', ['obj' => $travel->editFacet->id]), $request);
        $response
            ->assertStatus(204);
    }

    /**
     * @test
     *
     */
    public function update_travel_with_pilist()
    {
        $travel = factory(Travel::class)->create();
        $list = factory(OcapList::class)->create();
        $request = [
            'title' => 'Titre',
            'pis' => route('obj.show', ['obj' => $list->viewFacet->id])
        ];
        $response = $this->put(route('obj.update', ['obj' => $travel->editFacet->id]), $request);
        $response
            ->assertStatus(204);
    }

    /**
     * @test
     *
     */
    public function update_travel_via_view_facet()
    {
        $travel = factory(Travel::class)->create();
        $request = [
            'title' => 'Titre'
        ];
        $response = $this->put(route('obj.update', ['obj' => $travel->viewFacet->id]), $request);
        $response
            ->assertStatus(405);
    }

    /**
     * @test
     *
     */
    public function bad_update_travel()
    {
        $request = [
            'title' => 'Titre'
        ];
        $response = $this->put(route('obj.update', ['obj' => 'acenvironment']), $request);
        $response
            ->assertStatus(404);
    }

    /**
     * @test
     *
     */
    public function update_travel_with_bad_request()
    {
        $travel = factory(Travel::class)->create();
        $request = [
            'title' => [],
            'bad_field' => 'LPGdate'
        ];
        $response = $this->put(route('obj.update', ['obj' => $travel->editFacet->id]), $request);
        $response
            ->assertStatus(400);
    }

    /**
     * @test
     *
     */
    public function update_travel_with_bad_request_bis()
    {
        $travel = factory(Travel::class)->create();
        $request = [
            'title' => 'This is a title',
            'pis' => 'thisisnotafaceturl'
        ];

        $response = $this->put(route('obj.update', ['obj' => $travel->editFacet->id]), $request);
        $response
            ->assertStatus(400);

        $this->assertEquals($travel->title, Travel::find($travel->id)->title);
    }

    /**
     * @test
     *
     */
    public function delete_travel()
    {
        $travel = factory(Travel::class)->create();
        $response = $this->delete(route('obj.destroy', ['obj' => $travel->editFacet->id]));
        $response
            ->assertStatus(204);
    }

    /**
     * @test
     *
     */
    public function delete_travel_via_view_facet()
    {
        $travel = factory(Travel::class)->create();
        $response = $this->delete(route('obj.destroy', ['obj' => $travel->viewFacet->id]));
        $response
            ->assertStatus(405);
    }

    /**
     * @test
     *
     */
    public function bad_delete_travel()
    {
        $response = $this->delete(route('obj.destroy', ['obj' => 'badspycrossriver']));
        $response
            ->assertStatus(404);
    }
}
