<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserFeaturesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_create_account(): void
    {
        $sample_user = User::factory()->make();

        $endpoint = route('api.users.store', $sample_user->toArray());
        $response = $this->postJson($endpoint, [
            ...$sample_user->toArray(),
            "password" => 'password'
        ]);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'message', 'data'
        ]);

        $this->assertDatabaseHas(
            (new User)->getTable(),
            [
                "email" => $sample_user->email,
                "role" => $sample_user->role,
            ]
        );
    }

    /**
     * @test
     */
    public function user_can_view_another_user_information(): void
    {
        $user = User::factory()->create();
        $second_party_user = User::factory()->create();

        $endpoint = route('api.users.show', ['user' => $second_party_user->id]);
        Sanctum::actingAs($user);
        $response = $this->getJson($endpoint);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'message', 'data'
        ]);

        // confirm the user whose info is returned
        $response->assertJsonFragment(
            $second_party_user->only(['email', 'role'])
        );
    }

    /**
     * @test
     */
    public function user_can_view_own_profile(): void
    {
        $user = User::factory()->create();

        $endpoint = route('api.profile');
        Sanctum::actingAs($user);
        $response = $this->getJson($endpoint);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'message', 'data'
        ]);

        // confirm the user whose info is returned
        $response->assertJsonFragment(
            $user->only(['email', 'role'])
        );
    }

    /**
     * @test
     */
    public function user_can_delete_his_account(): void
    {
        $user = User::factory()->create();

        $endpoint = route('api.users.delete');
        Sanctum::actingAs($user);
        $response = $this->deleteJson($endpoint);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'message', 'data'
        ]);

        $this->assertDatabaseMissing(
            (new User)->getTable(),
            [
                "email" => $user->email,
            ]
        );
    }
}
