<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\OcapList;

use App\Models\Media;

use Eris\Generator,
    Eris\TestTrait;

class OcapListTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    /**
     * @test
     *
     */
    public function create_ocap_list()
    {
        $response = $this->post(route('list.store'));
        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                "type",
                "ocapType",
                "url"
            ]);
    }

    /**
    * @test
    *
    */
    public function view_ocap_list()
    {
        $ocaplist = factory(OcapList::class)->create();
        $response = $this->get(route('obj.show', ['obj' => $ocaplist->viewFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                "type",
                "contents"
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_view_ocap_list()
    {
        $response = $this->get(route('obj.show', ['obj' => "nimportequoi"]));
        $response
            ->assertStatus(404);
    }

    /**
     * @test
     *
     */
    public function edit_facet_ocap_list()
    {
        $ocaplist = factory(OcapList::class)->create();
        $response = $this->get(route('obj.show', ['obj' => $ocaplist->editFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                "type",
                "view_facet",
                "contents",
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_edit_facet_ocap_list()
    {
        $response = $this->get(route('obj.show', ['obj' => "dautreschoses"]));
        $response
            ->assertStatus(404);
    }

    /**
     * @test
     *
     */
    public function update_ocap_list()
    {
        $this->limitTo(10)->forAll(Generator\nat())->then(function ($nbr) {
            $ocaplist = factory(OcapList::class)->create();

            $empty_array = array_fill(0, $nbr, null);
            $ocaps = array_map(
                function ($value) {
                    $media = factory(Media::class)->create();
                    $route = rand(0, 1)
                        ? route('obj.show', ['obj' => $media->viewFacet->id])
                        : route('obj.show', ['obj' => $media->editFacet->id]);
                    return ($route);
                }, $empty_array);
            $response = $this->put(route('obj.update', ['obj' => $ocaplist->editFacet->id]), ["ocaps" => $ocaps]);
            $response
                ->assertStatus(204);
        });
    }

    /**
     * @test
     *
     */
    public function bad_update_ocap_list()
    {
        $this->limitTo(10)->forAll(Generator\pos())->then(function ($nbr) {
            $ocaplist = factory(OcapList::class)->create();

            $ocaps = array_fill(0, $nbr, str_random(40));
            $response = $this->put(route('obj.update', ['obj' => $ocaplist->editFacet->id]), ["ocaps" => $ocaps]);
            $response
                ->assertStatus(400);
        });

    }

    /**
     * @test
     *
     */
    public function bad_bis_update_ocap_list()
    {
        $ocaplist = factory(OcapList::class)->create();

        $response = $this->put(route('obj.update', ['obj' => $ocaplist->editFacet->id]), ["autrement" => []]);
        $response
            ->assertStatus(400);
    }

    /**
     * @test
     *
     */
    public function bad_ter_update_ocap_list()
    {
        $ocaplist = factory(OcapList::class)->create();

        $response = $this->put(route('obj.update', ['obj' => $ocaplist->editFacet->id]), ["ocaps" => 150]);
        $response
            ->assertStatus(400);
    }

    /**
     * @test
     *
     */
    public function bad_quater_update_ocap_list()
    {
        $response = $this->put(route('obj.update', ['obj' => '$ocaplist->editFacet->id']), ["ocaps" => 150]);
        $response
            ->assertStatus(404);
    }

    /**
     * @test
     *
     */
    public function delete_ocap_list()
    {
        $ocaplist = factory(OcapList::class)->create();
        $response = $this->delete(route('obj.destroy', ['obj' => $ocaplist->editFacet->id]));
        $response
            ->assertStatus(204);
    }

    /**
     * @test
     *
     */
    public function bad_delete_ocap_list()
    {
        $response = $this->delete(route('obj.destroy', ['obj' => "badbougieparfumee"]));
        $response
            ->assertStatus(404);
    }
}
