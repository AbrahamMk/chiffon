<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Domains\Catalog\Models\ProductVariant;
use App\Domains\Inventory\Models\InventoryTransaction;
use App\Domains\Inventory\Services\InventoryService;
use App\Domains\Settings\Services\SettingsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inventory\StoreStockAdjustmentRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class StockController extends Controller
{
    public function index(SettingsService $settings): Response
    {
        $this->authorize('inventory.manage');

        $threshold = (int) $settings->get('low_stock_threshold', 5);

        $lowStockVariants = ProductVariant::query()
            ->with(['product:id,name,sku'])
            ->where('is_active', true)
            ->where('stock_quantity', '<=', $threshold)
            ->orderBy('stock_quantity')
            ->paginate(15, ['*'], 'low_stock_page')
            ->withQueryString();

        $recentTransactions = InventoryTransaction::query()
            ->with([
                'productVariant:id,sku,product_id',
                'productVariant.product:id,name',
                'user:id,name',
            ])
            ->orderByDesc('created_at')
            ->paginate(20, ['*'], 'transactions_page')
            ->withQueryString();

        return Inertia::render('Admin/Inventory/Stock/Index', [
            'lowStockThreshold' => $threshold,
            'lowStockVariants' => $lowStockVariants,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    public function store(StoreStockAdjustmentRequest $request, InventoryService $inventory): RedirectResponse
    {
        $this->authorize('inventory.manage');

        $data = $request->validated();
        $variant = ProductVariant::query()->findOrFail($data['product_variant_id']);

        try {
            $inventory->adjust(
                $variant,
                (int) $data['quantity_change'],
                'adjust',
                $request->user()?->id,
                $data['reason'] ?? null,
                null,
                null,
                $data['inventory_location_id'] ?? null,
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['quantity_change' => $e->getMessage()]);
        }

        return back()->with('success', 'Stock adjusted.');
    }
}
