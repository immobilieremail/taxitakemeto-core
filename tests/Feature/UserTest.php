<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     */
    public function create_user()
    {
        $response = $this->post(route('user.store'), ['name' => 'Billy']);
        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJson([
                'type' => 'ocap',
                'ocapType' => 'UserProfileFacet',
                'url' => route('obj.show', ['obj' => User::first()->profileFacet->id])
            ]);
    }

    /**
     * @test
     *
     */
    public function bad_request_create_user()
    {
        $response = $this->post(route('user.store'), ['exact' => []]);
        $response
            ->assertStatus(302);
    }

    /**
     * @test
     *
     */
    public function get_user_profile_facet()
    {
        $user = factory(User::class)->create();
        $response = $this->get(route('obj.show', ['obj' => $user->profileFacet->id]));
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                'type',
                'data' => [
                    'name',
                    'email',
                    'phone',
                    'password'
                ]
            ]);
    }

    /**
     * @test
     *
     */
    public function get_bad_user_profile_facet()
    {
        $response = $this->get(route('obj.show', ['obj' => 'user profileFacet id']));
        $response
            ->assertStatus(404);
    }
}
