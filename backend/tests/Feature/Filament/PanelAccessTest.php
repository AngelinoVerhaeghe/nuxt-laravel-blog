<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\LatestOrders;
use App\Filament\Widgets\OrdersChart;
use App\Filament\Widgets\ProductsChart;
use App\Filament\Widgets\StatsOverview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_without_a_role_cannot_open_the_panel(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertForbidden();
    }

    public function test_editors_can_open_the_dashboard(): void
    {
        $this->actingAs(User::factory()->role('editor')->create())
            ->get('/dashboard')
            ->assertOk();
    }

    public static function shopWidgets(): array
    {
        return [
            'latest orders' => [LatestOrders::class],
            'orders chart' => [OrdersChart::class],
            'products chart' => [ProductsChart::class],
            'stats overview' => [StatsOverview::class],
        ];
    }

    #[DataProvider('shopWidgets')]
    public function test_only_admins_see_the_shop_widgets(string $widget): void
    {
        $this->actingAs(User::factory()->role('editor')->create());
        $this->assertFalse($widget::canView());

        $this->actingAs(User::factory()->role('admin')->create());
        $this->assertTrue($widget::canView());
    }

    public static function contentPaths(): array
    {
        return [
            'articles' => ['/dashboard/articles'],
            'categories' => ['/dashboard/categories'],
            'posts' => ['/dashboard/posts'],
            'tags' => ['/dashboard/tags'],
        ];
    }

    public static function adminOnlyPaths(): array
    {
        return [
            'brands' => ['/dashboard/brands'],
            'customers' => ['/dashboard/customers'],
            'orders' => ['/dashboard/orders'],
            'permissions' => ['/dashboard/permissions'],
            'products' => ['/dashboard/products'],
            'roles' => ['/dashboard/roles'],
            'users' => ['/dashboard/users'],
        ];
    }

    #[DataProvider('contentPaths')]
    public function test_editors_can_manage_blog_content(string $path): void
    {
        $this->actingAs(User::factory()->role('editor')->create())
            ->get($path)
            ->assertOk();
    }

    #[DataProvider('adminOnlyPaths')]
    public function test_editors_cannot_open_admin_only_resources(string $path): void
    {
        $this->actingAs(User::factory()->role('editor')->create())
            ->get($path)
            ->assertForbidden();
    }

    #[DataProvider('adminOnlyPaths')]
    public function test_admins_can_open_admin_only_resources(string $path): void
    {
        $this->actingAs(User::factory()->role('admin')->create())
            ->get($path)
            ->assertOk();
    }

    public function test_public_registration_is_disabled(): void
    {
        $this->post('/register', [
            'name' => 'Someone',
            'email' => 'someone@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'someone@example.com']);
    }
}
