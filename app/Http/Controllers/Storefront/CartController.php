<?php

namespace App\Http\Controllers\Storefront;

use App\Domains\Catalog\Models\ProductVariant;
use App\Domains\Orders\Models\CartItem;
use App\Domains\Orders\Services\CartService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(Request $request, CartService $carts): Response
    {
        $cart = $carts->resolve($request->user());

        return Inertia::render('Storefront/Cart/Index', [
            'cart' => $carts->summary($cart),
        ]);
    }

    public function store(Request $request, CartService $carts): RedirectResponse
    {
        $validated = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $variant = ProductVariant::query()
            ->where('is_active', true)
            ->whereHas('product', fn ($q) => $q->where('status', 'active'))
            ->findOrFail($validated['variant_id']);

        $cart = $carts->resolve($request->user());
        $carts->add($cart, $variant, (int) ($validated['quantity'] ?? 1));

        return back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartItem $cartItem, CartService $carts): RedirectResponse
    {
        $cart = $carts->resolve($request->user());
        abort_unless($cartItem->cart_id === $cart->id, 403);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $carts->updateQuantity($cartItem, (int) $validated['quantity']);

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Request $request, CartItem $cartItem, CartService $carts): RedirectResponse
    {
        $cart = $carts->resolve($request->user());
        abort_unless($cartItem->cart_id === $cart->id, 403);

        $carts->remove($cartItem);

        return back()->with('success', 'Item removed.');
    }
}
