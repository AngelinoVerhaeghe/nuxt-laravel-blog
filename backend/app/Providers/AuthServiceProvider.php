<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Policies\AdminOnlyPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Only admins can manage users, roles and the shop; editors get the blog content
        User::class => AdminOnlyPolicy::class,
        Role::class => AdminOnlyPolicy::class,
        Permission::class => AdminOnlyPolicy::class,
        Product::class => AdminOnlyPolicy::class,
        Brand::class => AdminOnlyPolicy::class,
        Customer::class => AdminOnlyPolicy::class,
        Order::class => AdminOnlyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        //
    }
}
