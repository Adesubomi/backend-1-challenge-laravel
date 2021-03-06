<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'deposit' => 0,
            'role' => $this->faker->randomElement(Role::cases()),
        ];
    }

    public function role(Role $role): UserFactory
    {
        return $this->state([
            'role' => $role,
        ]);
    }

    public function fund(int $amount = null): UserFactory
    {
        return $this->state([
            'deposit' => $amount ?? rand(2,40) * 5
        ]);
    }
}
