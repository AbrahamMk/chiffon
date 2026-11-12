<?php

namespace App\Http\Controllers\Storefront;

use App\Domains\Delivery\Models\DeliveryZone;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Services\CartService;
use App\Domains\Orders\Services\OrderService;
use App\Domains\Payments\Models\PaymentMethod;
use App\Domains\Settings\Services\SettingsService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class CheckoutController extends Controller
{
    public function create(Request $request, CartService $carts, SettingsService $settings): Response
    {
        $cart = $carts->resolve($request->user());
        $summary = $carts->summary($cart);

        return Inertia::render('Storefront/Checkout/Create', [
            'cart' => $summary,
            'deliveryZones' => DeliveryZone::query()->where('is_active', true)->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'guestCheckoutEnabled' => (bool) $settings->get('guest_checkout_enabled', true),
        ]);
    }

    public function store(Request $request, CartService $carts, OrderService $orders, SettingsService $settings): RedirectResponse
    {
        $guestEnabled = (bool) $settings->get('guest_checkout_enabled', true);
        if (! $guestEnabled && ! $request->user()) {
            abort(403, 'Guest checkout is disabled. Please sign in.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'fulfillment_type' => ['required', 'in:delivery,pickup'],
            'delivery_zone_id' => ['nullable', 'integer', 'exists:delivery_zones,id'],
            'city' => ['nullable', 'string', 'max:120'],
            'subcity' => ['nullable', 'string', 'max:120'],
            'address_line' => ['nullable', 'string', 'max:500'],
            'delivery_instructions' => ['nullable', 'string', 'max:1000'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'payment_method_id' => ['nullable', 'integer', 'exists:payment_methods,id'],
        ]);

        $cart = $carts->resolve($request->user());

        try {
            $order = $orders->createFromCart($cart, $validated, $request->user());
        } catch (RuntimeException $e) {
            return back()->withErrors(['checkout' => $e->getMessage()]);
        }

        return redirect()
            ->route('checkout.confirmation', $order->order_number)
            ->with('success', 'Order placed successfully.');
    }

    public function confirmation(string $orderNumber): Response
    {
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->with('items')
            ->firstOrFail();

        return Inertia::render('Storefront/Checkout/Confirmation', [
            'order' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'grand_total' => $order->grand_total,
                'items' => $order->items,
            ],
        ]);
    }
}
