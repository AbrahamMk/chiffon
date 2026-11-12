<?php

namespace Tests\Feature\Inventory;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductVariant;
use App\Domains\Inventory\Services\InventoryService;
use Database\Seeders\BusinessSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(BusinessSettingsSeeder::class);
    }

    public function test_stock_adjustment_records_transaction(): void
    {
        $variant = $this->makeVariant(10);

        app(InventoryService::class)->adjust($variant, 5, 'receive', null, 'Initial receive');

        $variant->refresh();
        $this->assertSame(15, (int) $variant->stock_quantity);
        $this->assertDatabaseHas('inventory_transactions', [
            'product_variant_id' => $variant->id,
            'type' => 'receive',
            'quantity_before' => 10,
            'quantity_change' => 5,
            'quantity_after' => 15,
        ]);
    }

    public function test_negative_stock_is_blocked_by_default(): void
    {
        $variant = $this->makeVariant(2);

        $this->expectException(RuntimeException::class);
        app(InventoryService::class)->adjust($variant, -5, 'adjust', null, 'Bad adjust');
    }

    private function makeVariant(int $stock): ProductVariant
    {
        $product = Product::query()->create([
            'name' => 'Test Product',
            'slug' => 'test-product-'.uniqid(),
            'base_price' => 100,
            'status' => 'active',
        ]);

        return ProductVariant::query()->create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.uniqid(),
            'name' => 'Default',
            'price' => 100,
            'stock_quantity' => $stock,
            'reserved_quantity' => 0,
            'is_active' => true,
        ]);
    }
}
