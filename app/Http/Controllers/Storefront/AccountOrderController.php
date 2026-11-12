<?php

namespace App\Http\Controllers\Storefront;

use App\Domains\Orders\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Order::query()
            ->withCount('items')
            ->latest();

        $user = $request->user();
        $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id);
            if ($user->customer) {
                $q->orWhere('customer_id', $user->customer->id);
            }
        });

        return Inertia::render('Storefront/Account/Orders/Index', [
            'orders' => $query->paginate(10),
        ]);
    }

    public function show(Request $request, string $orderNumber): Response
    {
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->with(['items', 'payments.paymentMethod', 'statusHistories'])
            ->firstOrFail();

        $user = $request->user();
        $owns = $order->user_id === $user->id
            || ($user->customer && $order->customer_id === $user->customer->id);

        abort_unless($owns, 403);

        return Inertia::render('Storefront/Account/Orders/Show', [
            'order' => $order,
        ]);
    }
}
