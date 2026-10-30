<?php

namespace Tests\Feature\Filament;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->role('admin')->create());

        $this->customer = Customer::forceCreate([
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

    public function test_creating_an_order_saves_the_chosen_status(): void
    {
        $brand = Brand::forceCreate(['name' => 'Funko', 'slug' => 'funko']);
        $product = Product::forceCreate([
            'brand_id' => $brand->id,
            'name' => 'Groot',
            'slug' => 'groot',
            'sku' => 'GROOT',
            'image' => 'groot.png',
            'quantity' => 5,
            'price' => 12.5,
            'published_at' => now(),
        ]);

        Livewire::test(CreateOrder::class)
            ->fillForm([
                'customer_id' => $this->customer->id,
                'shipping_price' => 5,
                'status' => OrderStatusEnum::PROCESSING->value,
                'notes' => 'Gift wrap',
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 12.5],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(OrderStatusEnum::PROCESSING, Order::sole()->status);
    }

    public function test_new_orders_default_to_pending(): void
    {
        Livewire::test(CreateOrder::class)
            ->assertSchemaStateSet(['status' => OrderStatusEnum::PENDING]);
    }

    public function test_editing_an_order_shows_and_saves_its_status(): void
    {
        $order = $this->createOrder('OR-1', OrderStatusEnum::PENDING);

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->assertSchemaStateSet(['status' => OrderStatusEnum::PENDING])
            ->fillForm(['status' => OrderStatusEnum::COMPLETED->value])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(OrderStatusEnum::COMPLETED, $order->refresh()->status);
    }

    public function test_an_order_can_be_saved_without_notes(): void
    {
        $order = $this->createOrder('OR-1', OrderStatusEnum::PENDING);

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->fillForm(['notes' => null])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull($order->refresh()->notes);
    }

    public function test_the_orders_list_shows_status_labels(): void
    {
        $this->createOrder('OR-1', OrderStatusEnum::PROCESSING);
        $this->createOrder('OR-2', OrderStatusEnum::DECLINED);

        Livewire::test(ListOrders::class)
            ->assertSee('Processing')
            ->assertSee('Declined');
    }

    public function test_the_navigation_badge_counts_orders_being_processed(): void
    {
        $this->createOrder('OR-1', OrderStatusEnum::PROCESSING);
        $this->createOrder('OR-2', OrderStatusEnum::PROCESSING);
        $this->createOrder('OR-3', OrderStatusEnum::PENDING);

        $this->assertEquals(2, OrderResource::getNavigationBadge());
        $this->assertSame('primary', OrderResource::getNavigationBadgeColor());
    }

    private function createOrder(string $number, OrderStatusEnum $status): Order
    {
        return Order::forceCreate([
            'customer_id' => $this->customer->id,
            'number' => $number,
            'status' => $status,
            'shipping_price' => 5,
        ]);
    }
}
