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
            'title' => 'Title',
            'description' => 'Description',
            'address' => '1 rue des iiyama'
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
    public function create_pi_with_medias()
    {
        $ocaplist = factory(OcapList::class)->create();
        $response = $this->post(route('pi.store'), [
            'title' => 'Title',
            'description' => 'Description',
            'address' => '1 rue des iiyama',
            'medias' => route('obj.show', $ocaplist->editFacet->id)
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
        $response = $this->get(route('obj.show', ['obj' => $pi->viewFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                'type',
                'data' => [
                    'title',
                    'description',
                    'medias'
                ]
            ]);
    }

    /**
     * @test
     *
     */
    public function view_pi_with_medias()
    {
        $pi = factory(PI::class)->create();
        $ocaplist = factory(OcapList::class)->create();

        $pi->mediaOcapListFacets()->save($ocaplist->viewFacet);
        $response = $this->get(route('obj.show', ['obj' => $pi->viewFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                'type',
                'data' => [
                    'title',
                    'description',
                    'medias'
                ]
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_view_pi()
    {
        $response = $this->get(route('obj.show', ['obj' => 'something bad']));
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
        $response = $this->get(route('obj.show', ['obj' => $pi->editFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                'type',
                'view_facet',
                'data' => [
                    'title',
                    'description',
                    'medias'
                ]
            ]);
    }

    /**
     * @test
     *
     */
    public function edit_pi_with_medias()
    {
        $pi = factory(PI::class)->create();
        $ocaplist = factory(OcapList::class)->create();

        $pi->mediaOcapListFacets()->save($ocaplist->viewFacet);
        $response = $this->get(route('obj.show', ['obj' => $pi->editFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                'type',
                'view_facet',
                'data' => [
                    'title',
                    'description',
                    'medias'
                ]
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_edit_pi()
    {
        $response = $this->get(route('obj.show', ['obj' => 'something very bad']));
        $response
            ->assertStatus(404);
    }

    /**
     * @test
     *
     */
    public function update_pi()
    {
        $pi = factory(PI::class)->create();
        $request = [
            'title' => 'Titre',
            'description' => 'Description du PI',
            'address' => '1 rue de la Tasse Bleue'
        ];
        $response = $this->put(route('obj.update', ['obj' => $pi->editFacet->id]), $request);
        $response
            ->assertStatus(204);
    }

    /**
     * @test
     *
     */
    public function update_pi_with_medialist()
    {
        $pi = factory(PI::class)->create();
        $list = factory(OcapList::class)->create();
        $request = [
            'title' => 'Titre',
            'description' => 'Description du PI',
            'address' => '1 rue de la Tasse Bleue',
            'medias' => route('obj.show', ['obj' => $list->viewFacet->id])
        ];
        $response = $this->put(route('obj.update', ['obj' => $pi->editFacet->id]), $request);
        $response
            ->assertStatus(204);
    }

    /**
     * @test
     *
     */
    public function update_pi_via_view_facet()
    {
        $pi = factory(PI::class)->create();
        $request = [
            'title' => 'Titre',
            'description' => 'Description du PI',
            'address' => '1 rue de la Tasse Bleue'
        ];
        $response = $this->put(route('obj.update', ['obj' => $pi->viewFacet->id]), $request);
        $response
            ->assertStatus(405);
    }

    /**
     * @test
     *
     */
    public function bad_update_pi()
    {
        $request = [
            'title' => 'Titre',
            'description' => 'Description du PI',
            'address' => '1 rue de la Tasse Bleue'
        ];
        $response = $this->put(route('obj.update', ['obj' => 'acenvironment']), $request);
        $response
            ->assertStatus(404);
    }

    /**
     * @test
     *
     */
    public function update_pi_with_bad_request()
    {
        $pi = factory(PI::class)->create();
        $request = [
            'title' => [],
            'bad_field' => 'LPGdate'
        ];
        $response = $this->put(route('obj.update', ['obj' => $pi->editFacet->id]), $request);
        $response
            ->assertStatus(400);
    }

    /**
     * @test
     *
     */
    public function update_pi_with_bad_request_bis()
    {
        $pi = factory(PI::class)->create();
        $request = [
            'title' => 'New title',
            'description' => 'A description that describe what the description describe.',
            'address' => '6 rue du Martin Pécheur',
            'medias' => 'arandomstringthatcannotbeafaceturl'
        ];
        $response = $this->put(route('obj.update', ['obj' => $pi->editFacet->id]), $request);
        $response
            ->assertStatus(400);

        $new_pi = PI::find($pi->id);
        $this->assertEquals($pi->title, $new_pi->title);
        $this->assertEquals($pi->description, $new_pi->description);
        $this->assertEquals($pi->address, $new_pi->address);
    }

    /**
     * @test
     *
     */
    public function delete_pi()
    {
        $pi = factory(PI::class)->create();
        $response = $this->delete(route('obj.destroy', ['obj' => $pi->editFacet->id]));
        $response
            ->assertStatus(204);
    }

    /**
     * @test
     *
     */
    public function delete_pi_via_view_facet()
    {
        $pi = factory(PI::class)->create();
        $response = $this->delete(route('obj.destroy', ['obj' => $pi->viewFacet->id]));
        $response
            ->assertStatus(405);
    }

    /**
     * @test
     *
     */
    public function bad_delete_pi()
    {
        $response = $this->delete(route('obj.destroy', ['obj' => 'badspycrossriver']));
        $response
            ->assertStatus(404);
    }
}
