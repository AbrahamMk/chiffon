<?php

namespace App\Domains\Orders\Services;

use App\Domains\Catalog\Models\ProductVariant;
use App\Domains\Customers\Models\Customer;
use App\Domains\Delivery\Models\Delivery;
use App\Domains\Delivery\Models\DeliveryZone;
use App\Domains\Inventory\Services\InventoryService;
use App\Domains\Marketing\Models\Coupon;
use App\Domains\Marketing\Models\CouponUsage;
use App\Domains\Orders\Models\Cart;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Orders\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class OrderService
{
    public function __construct(
        private InventoryService $inventory,
        private CartService $carts,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function createFromCart(Cart $cart, array $payload, ?User $actor = null): Order
    {
        $summary = $this->carts->summary($cart);

        if ($summary['items'] === []) {
            throw new RuntimeException('Cart is empty.');
        }

        return DB::transaction(function () use ($cart, $payload, $summary, $actor) {
            $subtotal = (float) $summary['subtotal'];
            $discount = 0.0;
            $coupon = null;

            if (! empty($payload['coupon_code'])) {
                $coupon = Coupon::query()->where('code', $payload['coupon_code'])->where('is_active', true)->first();
                if ($coupon) {
                    $discount = $this->calculateCouponDiscount($coupon, $subtotal);
                }
            }

            $deliveryFee = 0.0;
            if (($payload['fulfillment_type'] ?? 'delivery') === 'delivery' && ! empty($payload['delivery_zone_id'])) {
                $zone = DeliveryZone::query()->find($payload['delivery_zone_id']);
                $deliveryFee = $zone ? (float) $zone->fee : 0.0;
            }

            $grand = max(0, $subtotal - $discount + $deliveryFee);

            $customer = $this->resolveCustomer($payload, $actor);

            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $customer?->id,
                'user_id' => $actor?->id,
                'customer_name' => $payload['customer_name'],
                'customer_phone' => $payload['customer_phone'],
                'customer_email' => $payload['customer_email'] ?? null,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'fulfillment_type' => $payload['fulfillment_type'] ?? 'delivery',
                'subtotal' => $subtotal,
                'discount_total' => $discount,
                'delivery_fee' => $deliveryFee,
                'tax_total' => 0,
                'grand_total' => $grand,
                'amount_paid' => 0,
                'coupon_code' => $coupon?->code,
                'city' => $payload['city'] ?? null,
                'subcity' => $payload['subcity'] ?? null,
                'address_line' => $payload['address_line'] ?? null,
                'delivery_instructions' => $payload['delivery_instructions'] ?? null,
            ]);

            foreach ($summary['items'] as $row) {
                $variant = ProductVariant::query()->findOrFail($row['variant_id']);

                if ((int) $variant->stock_quantity < (int) $row['quantity']) {
                    throw new RuntimeException('Insufficient stock for '.$variant->sku);
                }

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $row['product_id'],
                    'product_variant_id' => $variant->id,
                    'product_name' => $row['product_name'],
                    'sku' => $row['sku'],
                    'variant_label' => $row['variant_label'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'line_total' => $row['line_total'],
                ]);

                $this->inventory->deductForSale(
                    $variant,
                    (int) $row['quantity'],
                    $actor?->id,
                    Order::class,
                    $order->id,
                );
            }

            if ($coupon) {
                $coupon->increment('used_count');
                CouponUsage::query()->create([
                    'coupon_id' => $coupon->id,
                    'order_id' => $order->id,
                    'customer_id' => $customer?->id,
                    'discount_amount' => $discount,
                ]);
            }

            $this->recordStatus($order, null, 'pending', $actor?->id, 'Order created');

            if (($payload['fulfillment_type'] ?? 'delivery') === 'delivery') {
                Delivery::query()->create([
                    'delivery_number' => 'DEL-'.now()->format('ymd').'-'.Str::upper(Str::random(4)),
                    'order_id' => $order->id,
                    'delivery_zone_id' => $payload['delivery_zone_id'] ?? null,
                    'status' => 'pending',
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'city' => $order->city,
                    'subcity' => $order->subcity,
                    'address_line' => $order->address_line,
                    'delivery_fee' => $deliveryFee,
                    'notes' => $order->delivery_instructions,
                ]);
            }

            $this->carts->clear($cart);

            return $order->fresh(['items', 'deliveries']);
        });
    }

    public function transition(Order $order, string $toStatus, ?User $actor = null, ?string $notes = null): Order
    {
        $allowed = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['ready_for_delivery', 'cancelled'],
            'ready_for_delivery' => ['shipped', 'cancelled'],
            'shipped' => ['delivered', 'returned'],
            'delivered' => ['returned'],
            'cancelled' => [],
            'returned' => [],
        ];

        $from = $order->status;
        if (! in_array($toStatus, $allowed[$from] ?? [], true)) {
            throw new RuntimeException("Cannot transition order from {$from} to {$toStatus}.");
        }

        return DB::transaction(function () use ($order, $from, $toStatus, $actor, $notes) {
            $order->update([
                'status' => $toStatus,
                'confirmed_at' => $toStatus === 'confirmed' ? now() : $order->confirmed_at,
                'cancelled_at' => $toStatus === 'cancelled' ? now() : $order->cancelled_at,
            ]);

            if ($toStatus === 'cancelled' && in_array($from, ['pending', 'confirmed', 'processing', 'ready_for_delivery'], true)) {
                foreach ($order->items as $item) {
                    if ($item->variant) {
                        $this->inventory->restoreAfterCancellation(
                            $item->variant,
                            (int) $item->quantity,
                            $actor?->id,
                            Order::class,
                            $order->id,
                        );
                    }
                }
            }

            $this->recordStatus($order, $from, $toStatus, $actor?->id, $notes);

            return $order->fresh();
        });
    }

    public function refreshPaymentStatus(Order $order): void
    {
        $paid = (float) $order->payments()->where('status', 'verified')->sum('amount');
        $order->amount_paid = $paid;

        $status = match (true) {
            $paid <= 0 => 'unpaid',
            $paid + 0.0001 < (float) $order->grand_total => 'partially_paid',
            default => 'paid',
        };

        $order->payment_status = $status;
        $order->save();
    }

    private function calculateCouponDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->min_order_amount && $subtotal < (float) $coupon->min_order_amount) {
            return 0;
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return 0;
        }

        if ($coupon->type === 'percent') {
            return round($subtotal * ((float) $coupon->value / 100), 2);
        }

        return min($subtotal, (float) $coupon->value);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function resolveCustomer(array $payload, ?User $actor): ?Customer
    {
        if ($actor?->customer) {
            return $actor->customer;
        }

        if (! empty($payload['customer_id'])) {
            return Customer::query()->find($payload['customer_id']);
        }

        return null;
    }

    private function recordStatus(Order $order, ?string $from, string $to, ?int $userId, ?string $notes): void
    {
        OrderStatusHistory::query()->create([
            'order_id' => $order->id,
            'user_id' => $userId,
            'from_status' => $from,
            'to_status' => $to,
            'notes' => $notes,
        ]);
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
