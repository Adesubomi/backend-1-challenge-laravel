<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'amount_available' => 0,
            'cost' => rand(1, 20) * 5,
            'product_name' => $this->faker->firstName() .' '. $this->faker->country(),
            'seller_id' => User::factory()->create()->id,
        ];
    }

    public function forUser(User $user): ProductFactory
    {
        return $this->state([
            'seller_id' => $user->id,
        ]);
    }
}
