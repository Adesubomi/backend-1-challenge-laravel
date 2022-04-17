<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    /**
     * @test
     */
    public function only_sellers_can_create_a_new_product(): void
    {
        $product = Product::factory()->make();
        $user = User::factory()->role(Role::Buyer)->create();

        $endpoint = route("api.products.store");

        Sanctum::actingAs($user);
        $response = self::postJson(
            $endpoint,
            $product->only(['amount_available', 'cost', 'product_name'])
        );
        $response->assertForbidden();

        $this->assertDatabaseMissing(
            (new Product())->getTable(),
            $product->only(['amount_available', 'cost', 'product_name'])
        );
    }

    /**
     * @test
     */
    public function seller_can_create_a_new_product(): void
    {
        $product = Product::factory()->make();
        $user = $product->seller;

        $endpoint = route("api.products.store");

        Sanctum::actingAs($user);
        $response = self::postJson(
            $endpoint,
            $product->only(['amount_available', 'cost', 'product_name'])
        );
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'message', 'data' => [
                'product'
            ]
        ]);
        $response->assertJsonFragment(
            $product->only(['amount_available', 'cost', 'product_name'])
        );

        $this->assertDatabaseHas(
            (new Product())->getTable(),
            $product->only(['amount_available', 'cost', 'product_name'])
        );
    }

    /**
     * @test
     */
    public function seller_can_update_a_product_details(): void
    {
        $product = Product::factory()->create();
        $product_info_for_update = Product::factory()->make();
        $user = $product->seller;

        $endpoint = route("api.products.update", ["product" => $product->id]);

        Sanctum::actingAs($user);
        $response = self::putJson(
            $endpoint,
            $product_info_for_update->only(['amount_available', 'cost', 'product_name'])
        );
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'message', 'data' => [
                'product'
            ]
        ]);

        $response->assertJsonFragment(
            $product_info_for_update->only([
                'amount_available',
                'cost',
                'product_name'
            ])
        );

        $this->assertDatabaseHas(
            (new Product())->getTable(),
            [
                "id" => $product->id,
                ...$product_info_for_update->only([
                    'amount_available',
                    'cost',
                    'product_name'
                ])
            ]
        );
    }

    /**
     * @test
     */
    public function seller_can_ONLY_update_his_product_details(): void
    {
        $product = Product::factory()->create();
        $product_info_for_update = Product::factory()->make();
        $imposter_seller = User::factory()->role(Role::Seller)->create();

        $endpoint = route("api.products.update", ["product" => $product->id]);

        Sanctum::actingAs($imposter_seller);
        $response = self::putJson(
            $endpoint,
            $product_info_for_update->only(['amount_available', 'cost', 'product_name'])
        );
        $response->assertForbidden();

        $this->assertDatabaseMissing(
            (new Product())->getTable(),
            [
                "id" => $product->id,
                ...$product_info_for_update->only([
                    'amount_available',
                    'cost',
                    'product_name'
                ])
            ]
        );

        $this->assertDatabaseHas(
            (new Product())->getTable(),
            $product->only(['id', 'amount_available', 'cost', 'product_name'])
        );
    }

    /**
     * @test
     */
    public function seller_can_delete_a_product(): void
    {
        $product = Product::factory()->create();
        $user = $product->seller;

        $endpoint = route("api.products.destroy", ["product" => $product->id]);

        Sanctum::actingAs($user);
        $response = self::deleteJson($endpoint);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'message', 'data'
        ]);

        $this->assertDatabaseMissing(
            (new Product())->getTable(),
            $product->only(['id'])
        );
    }

    /**
     * @test
     */
    public function seller_can_ONLY_delete_his_product(): void
    {
        $product = Product::factory()->create();
        $imposter_user = User::factory()->role(Role::Seller)->create();

        $endpoint = route("api.products.destroy", ["product" => $product->id]);

        Sanctum::actingAs($imposter_user);
        $response = self::deleteJson($endpoint);
        $response->assertForbidden();
        $this->assertDatabaseHas(
            (new Product())->getTable(),
            $product->only(['id'])
        );
    }

    /**
     * @test
     */
    public function buyer_can_buy_a_product(): void
    {
        $product = Product::factory()->amountAvailable(200)->create();
        $user = User::factory()->role(Role::Buyer)->fund(1_000_000)->create();
        $current_balance = $user->deposit;

        $endpoint = route("api.buy");

        Sanctum::actingAs($user);
        $response = self::postJson($endpoint, [
            'product_id' => $product->id,
            'amount' => 2
        ]);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'message', 'data' => [
                "product", "total_spent", "change", "total_change"
            ]
        ]);

        // Check that amount_available has reduced by the amount bought
        $this->assertDatabaseHas(
            (new Product())->getTable(),
            [
                'id' => $product->id,
                'amount_available' => $product->amount_available - 2,
            ]
        );

        // Check that the buyer_s balance has reduced
        $this->assertDatabaseHas(
            (new User)->getTable(),
            [
                'id' => $user->id,
                'deposit' => $current_balance - (2*$product->cost),
            ]
        );
    }

    /**
     * @test
     */
    public function only_buyer_can_buy_a_product(): void
    {
        $product = Product::factory()->amountAvailable(200)->create();
        $product_current_amount = $product->amount_available;

        $user = User::factory()->role(Role::Seller)->fund(1_000_000)->create();
        $current_balance = $user->deposit;

        $endpoint = route("api.buy");

        Sanctum::actingAs($user);
        $response = self::postJson($endpoint, [
            'product_id' => $product->id,
            'amount' => 2
        ]);
        $response->assertForbidden();

        // checking to make sure the amount has not changed
        $this->assertDatabaseHas(
            (new Product)->getTable(),
            ['amount_available' => $product_current_amount]
        );

        // check to make sure the buyer_s balance hasn_t changed
        $this->assertDatabaseHas(
            (new User)->getTable(),
            ['deposit' => $current_balance]
        );
    }

    /**
     * @test
     */
    public function buyer_can_NOT_buy_a_product_when_his_balance_can_not_cover_the_cost(): void
    {
        $product = Product::factory()->amountAvailable(200)->create();
        $product_current_amount = $product->amount_available;

        $user = User::factory()->role(Role::Buyer)->fund(5)->create();
        $current_balance = $user->deposit;

        $endpoint = route("api.buy");

        Sanctum::actingAs($user);
        $response = self::postJson($endpoint, [
            'product_id' => $product->id,
            'amount' => 2
        ]);

        $response->assertForbidden();

        // checking to make sure the amount has not changed
        $this->assertDatabaseHas(
            (new Product)->getTable(),
            ['amount_available' => $product_current_amount]
        );

        // check to make sure the buyer_s balance hasn_t changed
        $this->assertDatabaseHas(
            (new User)->getTable(),
            ['deposit' => $current_balance]
        );
    }

    /**
     * @test
     */
    public function buyer_can_NOT_buy_a_product_at_higher_amount_than_available_amount(): void
    {
        $product = Product::factory()->amountAvailable(1)->create();
        $product_current_amount = $product->amount_available;

        $user = User::factory()->role(Role::Buyer)->fund(1_000_000)->create();
        $current_balance = $user->deposit;

        $endpoint = route("api.buy");

        Sanctum::actingAs($user);
        $response = self::postJson($endpoint, [
            'product_id' => $product->id,
            'amount' => 2,
        ]);

        $response->assertForbidden();

        // checking to make sure the amount has not changed
        $this->assertDatabaseHas(
            (new Product)->getTable(),
            ['amount_available' => $product_current_amount]
        );

        // check to make sure the buyer_s balance hasn_t changed
        $this->assertDatabaseHas(
            (new User)->getTable(),
            ['deposit' => $current_balance]
        );
    }
}
