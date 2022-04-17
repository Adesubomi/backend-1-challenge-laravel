<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerGates();
    }

    private function registerGates()
    {
        Gate::define('deposit', function (User $user) {
            return Role::Buyer->match($user->role);
        });

        Gate::define('reset', function (User $user) {
            return Role::Buyer->match($user->role);
        });
    }
}
