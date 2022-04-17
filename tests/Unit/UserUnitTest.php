<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserUnitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function balanceAsChange(): void
    {
        /** @var User $user */
        $user = User::factory()->fund(5)->create();
        $this->assertEquals([5], $user->balanceAsChange());

        /** @var User $user */
        $user = User::factory()->fund(15)->create();
        $this->assertEquals([10, 5], $user->balanceAsChange());

        /** @var User $user */
        $user = User::factory()->fund(105)->create();
        $this->assertEquals([100, 5], $user->balanceAsChange());

        /** @var User $user */
        $user = User::factory()->fund(265)->create();
        $this->assertEquals([100, 100, 50, 10, 5], $user->balanceAsChange());

        /** @var User $user */
        $user = User::factory()->fund(185)->create();
        $this->assertEquals([100, 50, 20, 10, 5], $user->balanceAsChange());

        /** @var User $user */
        $user = User::factory()->fund(187)->create();
        $this->assertEquals([100, 50, 20, 10, 5], $user->balanceAsChange());
    }
}
