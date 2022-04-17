<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response
    {
        return $this->allow();
    }

    public function view(User $user, Product $product): Response
    {
        return $this->allow();
    }

    public function create(User $user): Response
    {
        if (!Role::Seller->match($user->role, true)) {
            return $this->deny();
        }
        return $this->allow();
    }

    public function update(User $user, Product $product): Response
    {
        if (!Role::Seller->match($user->role, true)) {
            return $this->deny();
        }
        if ($product->seller_id !== $user->id) {
            return $this->deny();
        }

        return $this->allow();
    }

    public function delete(User $user, Product $product): Response
    {
        if (!Role::Seller->match($user->role, true)) {
            return $this->deny();
        }
        if ($product->seller_id !== $user->id) {
            return $this->deny();
        }

        return $this->allow();
    }

    public function restore(User $user, Product $product): Response
    {
        return $this->deny();
    }

    public function forceDelete(User $user, Product $product): Response
    {
        return $this->deny();
    }

    public function buy(User $user, Product $product, int $amount): Response
    {
        if (!Role::Buyer->match($user->role)) {
            return $this->deny();
        }

        $total_spend = $product->cost * $amount;
        if ($user->deposit < $total_spend) {
            return $this->deny("You do not have enough deposit to buy this product");
        }

        if ($product->amount_available < $amount) {
            return $this->deny("Product not available. Only {$product->amount_available} left in stock. ");
        }

        return $this->allow();
    }
}
