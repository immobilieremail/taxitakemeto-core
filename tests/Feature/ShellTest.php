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
                'data' => [
                    'travels',
                    'contacts'
                ]
            ]);
    }

    /**
     * @test
     *
     */
    public function get_shell_user_facet_with_travel_list()
    {
        $shell = factory(Shell::class)->create();
        $travelList = factory(OcapList::class)->create();
        $contactList = factory(OcapList::class)->create();

        $shell->travelOcapListFacets()->save($travelList->editFacet);
        $shell->contactOcapListFacets()->save($contactList->editFacet);
        $response = $this->get(route('obj.show', ['obj' => $shell->userFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJson([
                'type' => 'ShellUserFacet',
                'data' => [
                    'travels' => route('obj.show', ['obj' => $travelList->editFacet]),
                    'contacts' => route('obj.show', ['obj' => $contactList->editFacet])
                ]
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
    public function update_shell_travel_list_via_dropbox_facet()
    {
        $shell = factory(Shell::class)->create();
        $ocaplist = factory(OcapList::class)->create();
        $request = [
            'travels' => route('obj.show', ['obj' => $ocaplist->editFacet->id])
        ];

        $response = $this->put(route('obj.update', ['obj' => $shell->dropboxFacet->id]), $request);
        $response
            ->assertStatus(405);
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

    /**
     * @test
     *
     */
    public function update_shell_contact_list()
    {
        $shell = factory(Shell::class)->create();
        $ocaplist = factory(OcapList::class)->create();
        $request = [
            'contacts' => route('obj.show', ['obj' => $ocaplist->editFacet])
        ];

        $response = $this->put(route('obj.update', ['obj' => $shell->userFacet]), $request);
        $response
            ->assertStatus(204);

        $this->assertEquals($ocaplist->editFacet->id, $shell->contactOcapListFacets->first()->id);
    }

    /**
     * @test
     *
     */
    public function update_shell_contact_list_via_dropbox_facet()
    {
        $shell = factory(Shell::class)->create();
        $ocaplist = factory(OcapList::class)->create();
        $request = [
            'contacts' => route('obj.show', ['obj' => $ocaplist->editFacet->id])
        ];

        $response = $this->put(route('obj.update', ['obj' => $shell->dropboxFacet->id]), $request);
        $response
            ->assertStatus(405);
    }

    /**
     * @test
     *
     */
    public function bad_request_update_shell_contact_list()
    {
        $shell = factory(Shell::class)->create();
        $request = [
            'contacts' => route('obj.show', ['obj' => 'saumon de Norvège'])
        ];

        $response = $this->put(route('obj.update', ['obj' => $shell->userFacet]), $request);
        $response
            ->assertStatus(400);
    }

    /**
     * @test
     *
     */
    public function update_shell_lists()
    {
        $shell = factory(Shell::class)->create();
        $travelList = factory(OcapList::class)->create();
        $contactList = factory(OcapList::class)->create();
        $request = [
            'travels' => route('obj.show', ['obj' => $travelList->editFacet]),
            'contacts' => route('obj.show', ['obj' => $contactList->editFacet])
        ];

        $response = $this->put(route('obj.update', ['obj' => $shell->userFacet]), $request);
        $response
            ->assertStatus(204);

        $this->assertEquals($travelList->editFacet->id, $shell->travelOcapListFacets->first()->id);
        $this->assertEquals($contactList->editFacet->id, $shell->contactOcapListFacets->first()->id);
    }
}
