<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductFeaturesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function can_view_list_of_all_products(): void
    {
        $user = User::factory()->role(Role::Buyer)->create();
        $endpoint = route("api.products.index");
        $products = Product::factory()->count(rand(2, 4))->create();

        Sanctum::actingAs($user);
        $response = self::getJson($endpoint);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'message', 'data'
        ]);

        $response_body = $response->getData()->data;
        self::assertCount($products->count(), $response_body->products->data);
    }

    /**
     * @test
     */
    public function seller_can_view_the_list_of_products_created_by_him(): void
    {
        $user = User::factory()->role(Role::Seller)->create();
        $endpoint = route("api.users.products");
        $products = Product::factory()
            ->forUser($user)
            ->count(4)
            ->create();
        $some_other_products = Product::factory()
            ->count(rand(2, 4))
            ->create();

        Sanctum::actingAs($user);
        $response = self::getJson($endpoint);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'message', 'data'
        ]);

        $response_body = $response->getData()->data;
        self::assertCount($products->count(), $response_body->products->data);
    }

    /**
     * @test
     */
    public function buyer_should_not_be_able_view_the_list_of_products_created_by_him(): void
    {
        $user = User::factory()->role(Role::Buyer)->create();
        $endpoint = route("api.users.products");

        Sanctum::actingAs($user);
        $response = self::getJson($endpoint);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function user_can_view_the_details_of_a_product(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create();
        $endpoint = route("api.products.show", ['product' => $product->id]);

        Sanctum::actingAs($user);
        $response = self::getJson($endpoint);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'message', 'data' => [
                'product'
            ]
        ]);

        $response->assertJsonFragment($product->only('product_name'));
    }
}
