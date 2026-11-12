<?php

namespace Tests\Feature\Payments;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\PaymentMethod;
use App\Domains\Payments\Services\PaymentService;
use App\Models\User;
use Database\Seeders\BusinessSettingsSeeder;
use Database\Seeders\CommerceDemoSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(BusinessSettingsSeeder::class);
        $this->seed(CommerceDemoSeeder::class);
    }

    public function test_partial_then_full_payment_updates_status(): void
    {
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $owner->assignRole('owner');

        $order = Order::query()->create([
            'order_number' => 'ORD-TEST-001',
            'customer_name' => 'Pay Test',
            'customer_phone' => '0911222333',
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'fulfillment_type' => 'pickup',
            'subtotal' => 5000,
            'discount_total' => 0,
            'delivery_fee' => 0,
            'tax_total' => 0,
            'grand_total' => 5000,
            'amount_paid' => 0,
        ]);

        $method = PaymentMethod::query()->where('code', 'cash')->firstOrFail();
        $payments = app(PaymentService::class);

        $first = $payments->record($order, [
            'payment_method_id' => $method->id,
            'amount' => 2000,
        ], $owner);
        $payments->verify($first, $owner);

        $order->refresh();
        $this->assertSame('partially_paid', $order->payment_status);
        $this->assertEquals(2000, (float) $order->amount_paid);

        $second = $payments->record($order, [
            'payment_method_id' => $method->id,
            'amount' => 3000,
        ], $owner);
        $payments->verify($second, $owner);

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertEquals(5000, (float) $order->amount_paid);
    }
}
