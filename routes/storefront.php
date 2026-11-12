<?php

use App\Http\Controllers\Storefront\AccountOrderController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\CustomOrderController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\NewsletterController;
use App\Http\Controllers\Storefront\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

Route::get('/custom-orders', [CustomOrderController::class, 'create'])->name('custom-orders.create');
Route::post('/custom-orders', [CustomOrderController::class, 'store'])->name('custom-orders.store');
Route::get('/custom-orders/{reference}', [CustomOrderController::class, 'show'])->name('custom-orders.show');

Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/orders', [AccountOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderNumber}', [AccountOrderController::class, 'show'])->name('orders.show');
});

Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

Route::get('/collections', fn () => inertia('Storefront/Placeholder', [
    'title' => 'Collections',
    'message' => 'Collections will be managed from the catalog module.',
]))->name('collections.index');

Route::get('/about', fn () => inertia('Storefront/Placeholder', [
    'title' => 'About Aster',
    'message' => 'Brand story content is configurable from Settings.',
]))->name('about');

Route::get('/contact', fn () => inertia('Storefront/Placeholder', [
    'title' => 'Contact',
    'message' => 'Contact details are configurable. No invented phone numbers are shown.',
]))->name('contact');
