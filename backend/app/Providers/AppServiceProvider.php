<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Policies\AdminOnlyPolicy;
use Filament\Facades\Filament;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        Schema::defaultStringLength(191);

        // Only admins can manage users, roles and the shop; editors get the blog content
        foreach ([User::class, Role::class, Permission::class, Product::class, Brand::class, Customer::class, Order::class] as $model) {
            Gate::policy($model, AdminOnlyPolicy::class);
        }

        // Reset links from POST /forgot-password open the admin panel's reset page, as
        // the frontend has no accounts (the panel's own "Forgot password?" does the same)
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return Filament::getPanel('dashboard')->getResetPasswordUrl($token, $notifiable);
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
