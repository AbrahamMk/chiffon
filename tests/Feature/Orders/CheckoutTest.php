<?php

namespace Tests\Feature\Orders;

use App\Domains\Catalog\Models\ProductVariant;
use App\Domains\Delivery\Models\DeliveryZone;
use App\Domains\Orders\Models\Order;
use Database\Seeders\BusinessSettingsSeeder;
use Database\Seeders\CommerceDemoSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(BusinessSettingsSeeder::class);
        $this->seed(CommerceDemoSeeder::class);
    }

    public function test_guest_can_checkout_and_stock_is_deducted(): void
    {
        $variant = ProductVariant::query()->where('sku', 'SAMPLE-CD-M')->firstOrFail();
        $before = (int) $variant->stock_quantity;
        $zone = DeliveryZone::query()->where('code', 'AA-CENTRAL')->firstOrFail();

        $this->from(route('shop.show', 'chiffon-dress-sample'))
            ->post(route('cart.store'), [
                'variant_id' => $variant->id,
                'quantity' => 2,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseCount('cart_items', 1);

        $response = $this->from(route('checkout.create'))
            ->post(route('checkout.store'), [
                'customer_name' => 'Guest Buyer',
                'customer_phone' => '0911000000',
                'customer_email' => 'guest@example.com',
                'fulfillment_type' => 'delivery',
                'delivery_zone_id' => $zone->id,
                'city' => 'Addis Ababa',
                'subcity' => 'Bole',
                'address_line' => 'Sample street',
                'coupon_code' => null,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('checkout.confirmation', Order::query()->first()->order_number));
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Guest Buyer',
            'status' => 'pending',
        ]);

        $variant->refresh();
        $this->assertSame($before - 2, (int) $variant->stock_quantity);
    }

    public function test_shop_lists_active_products(): void
    {
        $this->get(route('shop.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Storefront/Shop/Index'));
    }
}
