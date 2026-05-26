<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Mendefinisikan hak akses administrator
        Gate::define('is-admin', function (User $user) {
            return $user->role === 'Administrator';
        });

        // Mendefinisikan hak akses supervisor
        Gate::define('is-supervisor', function (User $user) {
            return $user->role === 'Supervisor';
        });
    }
}