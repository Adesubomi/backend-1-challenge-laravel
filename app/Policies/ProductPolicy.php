<?php

namespace App\Policies;

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
        return $this->allow();
    }

    public function update(User $user, Product $product): Response
    {
        return $this->allow();
    }

    public function delete(User $user, Product $product): Response
    {
        return $this->allow();
    }

    public function restore(User $user, Product $product): Response
    {
        return $this->allow();
    }

    public function forceDelete(User $user, Product $product): Response
    {
        return $this->allow();
    }
}
