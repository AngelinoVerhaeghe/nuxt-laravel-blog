<?php

namespace Tests\Feature\Filament;

use App\Enums\OrderStatusEnum;
use App\Filament\Widgets\BlogStatsOverview;
use App\Filament\Widgets\LatestPosts;
use App\Filament\Widgets\OrdersChart;
use App\Filament\Widgets\ProductsChart;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardWidgetsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-15 12:00:00');
    }

    public function test_the_dashboard_renders_for_editors(): void
    {
        $this->actingAs(User::factory()->role('editor')->create())
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_the_blog_stats_count_published_posts_and_drafts(): void
    {
        $user = User::factory()->role('editor')->create();
        $this->createPost($user, 'Old post', published: true, createdAt: '2026-08-01');
        $this->createPost($user, 'New post', published: true, createdAt: '2026-10-02');
        $this->createPost($user, 'Draft', published: false, createdAt: '2026-10-03');

        $this->actingAs($user);

        Livewire::test(BlogStatsOverview::class)
            ->assertSeeInOrder(['Published posts', '2', '1 new post this month'])
            ->assertSeeInOrder(['Drafts', '1', 'Not on the site yet']);
    }

    public function test_latest_posts_lists_drafts_and_published_posts(): void
    {
        $user = User::factory()->role('editor')->create();
        $this->createPost($user, 'Live post', published: true, createdAt: '2026-10-01');
        $this->createPost($user, 'Unfinished post', published: false, createdAt: '2026-10-02');

        $this->actingAs($user);

        Livewire::test(LatestPosts::class)
            ->assertSeeInOrder(['Unfinished post', 'Draft', 'Live post', 'Published']);
    }

    public function test_the_orders_chart_has_one_bar_per_status_in_order(): void
    {
        $customer = $this->createCustomer();
        $this->createOrder($customer, 'ORD-1', OrderStatusEnum::COMPLETED);
        $this->createOrder($customer, 'ORD-2', OrderStatusEnum::COMPLETED);
        $this->createOrder($customer, 'ORD-3', OrderStatusEnum::PENDING);

        $data = (fn () => $this->getData())->call(new OrdersChart());

        $this->assertSame(['Pending', 'Processing', 'Completed', 'Declined'], $data['labels']);
        $this->assertSame([1, 0, 2, 0], $data['datasets'][0]['data']);
    }

    public function test_the_products_chart_counts_the_last_twelve_months(): void
    {
        $brand = Brand::forceCreate(['name' => 'Funko', 'slug' => 'funko']);
        $this->createProduct($brand, 'a', createdAt: '2026-10-01');
        $this->createProduct($brand, 'b', createdAt: '2026-07-20');
        $this->createProduct($brand, 'c', createdAt: '2026-07-21');
        $this->createProduct($brand, 'd', createdAt: '2025-10-31'); // just outside the 12 months

        $data = (fn () => $this->getData())->call(new ProductsChart());

        $this->assertCount(12, $data['labels']);
        $this->assertSame('Nov 2025', $data['labels'][0]);
        $this->assertSame('Oct 2026', $data['labels'][11]);
        $this->assertSame([0, 0, 0, 0, 0, 0, 0, 0, 2, 0, 0, 1], $data['datasets'][0]['data']);
    }

    private function createPost(User $user, string $title, bool $published, string $createdAt): Post
    {
        $category = Category::firstOrCreate(['slug' => 'news'], ['name' => 'News']);

        return Post::forceCreate([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => $title,
            'slug' => str($title)->slug(),
            'description' => 'Body',
            'is_published' => $published,
            'created_at' => $createdAt,
        ]);
    }

    private function createCustomer(): Customer
    {
        return Customer::forceCreate([
            'name' => 'Jane',
            'email' => 'jane@example.com',
            'phone' => '0123',
            'date_of_birth' => '1990-01-01',
            'address' => 'Street 1',
            'zip_code' => '1000',
            'city' => 'Brussels',
            'country' => 'Belgium',
        ]);
    }

    private function createOrder(Customer $customer, string $number, OrderStatusEnum $status): Order
    {
        return Order::forceCreate([
            'customer_id' => $customer->id,
            'number' => $number,
            'status' => strtolower($status->value), // the column only accepts lowercase
            'notes' => '',
        ]);
    }

    private function createProduct(Brand $brand, string $name, string $createdAt): Product
    {
        return Product::forceCreate([
            'brand_id' => $brand->id,
            'name' => $name,
            'slug' => $name,
            'sku' => $name,
            'image' => 'image.png',
            'quantity' => 1,
            'price' => 10,
            'published_at' => $createdAt,
            'created_at' => $createdAt,
        ]);
    }
}
