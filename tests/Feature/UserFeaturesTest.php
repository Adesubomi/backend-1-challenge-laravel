<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserFeaturesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     * @test
     */
    public function can_create_account(): void
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
}
