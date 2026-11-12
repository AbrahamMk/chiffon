<?php

namespace App\Domains\Orders\Services;

use App\Domains\Catalog\Models\ProductVariant;
use App\Domains\Orders\Models\Cart;
use App\Domains\Orders\Models\CartItem;
use App\Models\User;
use App\Support\MediaUploader;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartService
{
    public function resolve(?User $user = null): Cart
    {
        if ($user) {
            $cart = Cart::query()->firstOrCreate(['user_id' => $user->id]);

            $guestToken = Session::get('cart_token');
            if ($guestToken) {
                $guestCart = Cart::query()->whereNull('user_id')->where('session_id', $guestToken)->first();
                if ($guestCart && $guestCart->id !== $cart->id) {
                    $this->mergeGuestCart($guestCart, $cart);
                }
            }

            return $cart;
        }

        $token = Session::get('cart_token');
        if (! $token) {
            $token = (string) Str::uuid();
            Session::put('cart_token', $token);
        }

        return Cart::query()->firstOrCreate(
            ['session_id' => $token],
            ['user_id' => null]
        );
    }

    public function add(Cart $cart, ProductVariant $variant, int $quantity = 1): CartItem
    {
        $item = CartItem::query()->firstOrNew([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
        ]);

        $item->quantity = ($item->exists ? (int) $item->quantity : 0) + max(1, $quantity);
        $item->save();

        return $item;
    }

    public function updateQuantity(CartItem $item, int $quantity): void
    {
        if ($quantity < 1) {
            $item->delete();

            return;
        }

        $item->update(['quantity' => $quantity]);
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, subtotal: string}
     */
    public function summary(Cart $cart): array
    {
        $cart->load(['items.variant.product.images', 'items.variant.attributeValues.attribute']);

        $items = [];
        $subtotal = 0;

        foreach ($cart->items as $item) {
            $variant = $item->variant;
            if (! $variant || ! $variant->is_active) {
                continue;
            }

            $line = (float) $variant->price * (int) $item->quantity;
            $subtotal += $line;

            $items[] = [
                'id' => $item->id,
                'variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'product_name' => $variant->product?->name,
                'sku' => $variant->sku,
                'variant_label' => $variant->name,
                'quantity' => (int) $item->quantity,
                'unit_price' => $variant->price,
                'line_total' => number_format($line, 2, '.', ''),
                'stock_quantity' => (int) $variant->stock_quantity,
                'image' => app(MediaUploader::class)->url(
                    $variant->product?->images?->firstWhere('is_primary', true)?->path
                        ?? $variant->product?->images?->first()?->path
                ),
            ];
        }

        return [
            'items' => $items,
            'subtotal' => number_format($subtotal, 2, '.', ''),
        ];
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }

    private function mergeGuestCart(Cart $guestCart, Cart $userCart): void
    {
        foreach ($guestCart->items as $item) {
            $this->add($userCart, $item->variant, (int) $item->quantity);
        }

        $guestCart->items()->delete();
        $guestCart->delete();
        Session::forget('cart_token');
    }
}
