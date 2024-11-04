<?php

namespace App\Providers;

use App\Models\Can;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        foreach ( Can::cases() as $can) {
            Gate::define(
                str($can->value)->snake('-')->toString(),
                fn (User $user) => $user->hasPermissionTo('be an admin')
            );
        }
    }
}
