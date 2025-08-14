<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Model => Policy::class
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('review purchases', function (User $user) {
            // TODO: adapt to your dynamic role logic
            // e.g. bool column:
            // return (bool) $user->is_admin;
            // or custom relation:
            // return $user->roles()->where('name', 'reviewer')->exists();
            return (bool) ($user->is_admin ?? false);
        });
    }
}
