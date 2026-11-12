<?php

use App\Http\Controllers\Admin\Catalog\CategoryController;
use App\Http\Controllers\Admin\Catalog\ProductController;
use App\Http\Controllers\Admin\Catalog\ProductImageController;
use App\Http\Controllers\Admin\Customer\CustomerController;
use App\Http\Controllers\Admin\CustomOrder\CustomOrderController as AdminCustomOrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Delivery\DeliveryController;
use App\Http\Controllers\Admin\Inventory\StockController;
use App\Http\Controllers\Admin\Inventory\SupplierController;
use App\Http\Controllers\Admin\Marketing\CouponController;
use App\Http\Controllers\Admin\Marketing\PromotionController;
use App\Http\Controllers\Admin\Marketing\SocialPostController;
use App\Http\Controllers\Admin\Order\OrderController;
use App\Http\Controllers\Admin\Payment\PaymentController;
use App\Http\Controllers\Admin\Production\ProductionJobController;
use App\Http\Controllers\Admin\Report\ReportController;
use App\Http\Controllers\Admin\Settings\BrandSettingsController;
use App\Http\Controllers\Admin\Settings\OperationalSettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'staff'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::prefix('catalog')->name('catalog.')->group(function () {
            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('products', ProductController::class);
            Route::post('products/{product}/archive', [ProductController::class, 'archive'])->name('products.archive');
            Route::post('products/{product}/images', [ProductImageController::class, 'store'])->name('products.images.store');
            Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy'])->name('products.images.destroy');
            Route::patch('products/{product}/images/{image}/primary', [ProductImageController::class, 'makePrimary'])->name('products.images.primary');
        });

        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('stock', [StockController::class, 'index'])->name('stock.index');
            Route::post('stock', [StockController::class, 'store'])->name('stock.store');
            Route::resource('suppliers', SupplierController::class)->except(['show']);
        });

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/orders/{order}/payments', [PaymentController::class, 'store'])->name('orders.payments.store');
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');

        Route::get('/custom-orders', [AdminCustomOrderController::class, 'index'])->name('custom-orders.index');
        Route::get('/custom-orders/{customOrder}', [AdminCustomOrderController::class, 'show'])->name('custom-orders.show');
        Route::patch('/custom-orders/{customOrder}/status', [AdminCustomOrderController::class, 'updateStatus'])->name('custom-orders.update-status');
        Route::post('/custom-orders/{customOrder}/quotations', [AdminCustomOrderController::class, 'storeQuotation'])->name('custom-orders.quotations.store');

        Route::get('/production', [ProductionJobController::class, 'index'])->name('production.index');
        Route::post('/production', [ProductionJobController::class, 'store'])->name('production.store');
        Route::get('/production/{productionJob}', [ProductionJobController::class, 'show'])->name('production.show');
        Route::patch('/production/stages/{stage}', [ProductionJobController::class, 'updateStage'])->name('production.stages.update');

        Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/{delivery}', [DeliveryController::class, 'show'])->name('deliveries.show');
        Route::patch('/deliveries/{delivery}/assign', [DeliveryController::class, 'assign'])->name('deliveries.assign');
        Route::patch('/deliveries/{delivery}/status', [DeliveryController::class, 'updateStatus'])->name('deliveries.update-status');

        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

        Route::prefix('marketing')->name('marketing.')->group(function () {
            Route::resource('coupons', CouponController::class)->except(['show']);
            Route::resource('promotions', PromotionController::class)->except(['show']);
            Route::resource('social-posts', SocialPostController::class)->except(['show'])->parameters([
                'social-posts' => 'socialPost',
            ]);
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/payments', [ReportController::class, 'payments'])->name('payments');
            Route::get('/sales/export', [ReportController::class, 'exportSales'])->name('sales.export');
        });

        Route::get('/settings/brand', [BrandSettingsController::class, 'edit'])->name('settings.brand.edit');
        Route::put('/settings/brand', [BrandSettingsController::class, 'update'])->name('settings.brand.update');

        Route::get('/settings/operational', [OperationalSettingsController::class, 'edit'])->name('settings.operational.edit');
        Route::put('/settings/operational', [OperationalSettingsController::class, 'update'])->name('settings.operational.update');
    });
