<?php

namespace Tests\Feature\Api;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(array $attributes = []): Product
    {
        $brand = Brand::firstOrCreate(['slug' => 'funko'], ['name' => 'Funko']);

        return Product::create([...[
            'brand_id' => $brand->id,
            'name' => 'Pikachu',
            'slug' => 'pikachu',
            'sku' => 'FUN-001',
            'image' => 'pikachu.png',
            'quantity' => 5,
            'price' => 14.99,
            'is_visible' => true,
            'type' => 'Pop!',
            'published_at' => '2026-01-01',
        ], ...$attributes]);
    }

    public function test_products_can_be_listed(): void
    {
        $this->createProduct();

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'pikachu')
            ->assertJsonPath('data.0.brand.name', 'Funko');
    }

    public function test_a_product_can_be_shown_by_slug(): void
    {
        $this->createProduct();

        $this->getJson('/api/v1/products/pikachu')
            ->assertOk()
            ->assertJsonPath('data.sku', 'FUN-001')
            ->assertJsonPath('data.type', 'Pop!');
    }

    public function test_hidden_and_not_yet_available_products_are_not_listed(): void
    {
        $this->createProduct();
        $this->createProduct(['slug' => 'hidden', 'sku' => 'FUN-002', 'is_visible' => false]);
        $this->createProduct(['slug' => 'coming-soon', 'sku' => 'FUN-003', 'published_at' => now()->addDay()]);

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'pikachu');
    }

    public function test_a_product_available_from_today_is_listed(): void
    {
        $this->createProduct(['published_at' => now()->toDateString()]);

        $this->getJson('/api/v1/products')->assertJsonCount(1, 'data');
    }

    public function test_a_hidden_product_returns_404(): void
    {
        $this->createProduct(['is_visible' => false]);

        $this->getJson('/api/v1/products/pikachu')->assertNotFound();
    }

    public function test_a_not_yet_available_product_returns_404(): void
    {
        $this->createProduct(['published_at' => now()->addDay()]);

        $this->getJson('/api/v1/products/pikachu')->assertNotFound();
    }

    public function test_an_unknown_product_returns_404(): void
    {
        $this->getJson('/api/v1/products/does-not-exist')->assertNotFound();
    }
}
