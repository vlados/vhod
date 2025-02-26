<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Дефиниране на Gate за управление на потребители (само за админ и мениджър)
        Gate::define('manage-users', function (User $user) {
            return in_array($user->role, ['admin', 'manager']);
        });
        
        // Дефиниране на Gate за администраторска функционалност
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });
        
        // Дефиниране на Gate за домоуправителска функционалност
        Gate::define('manage', function (User $user) {
            return in_array($user->role, ['admin', 'manager']);
        });
        
        // Дефиниране на Gate за собственици на апартаменти
        Gate::define('owner', function (User $user) {
            return in_array($user->role, ['admin', 'manager', 'owner']);
        });
    }
}
