<?php

namespace App\Domains\Inventory\Services;

use App\Domains\Catalog\Models\ProductVariant;
use App\Domains\Inventory\Models\InventoryTransaction;
use App\Domains\Settings\Services\SettingsService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class InventoryService
{
    public function __construct(private SettingsService $settings) {}

    public function adjust(
        ProductVariant $variant,
        int $change,
        string $type,
        ?int $userId = null,
        ?string $reason = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $locationId = null,
    ): InventoryTransaction {
        return DB::transaction(function () use ($variant, $change, $type, $userId, $reason, $referenceType, $referenceId, $locationId) {
            $locked = ProductVariant::query()->whereKey($variant->id)->lockForUpdate()->firstOrFail();
            $before = (int) $locked->stock_quantity;
            $after = $before + $change;

            if ($after < 0 && ! $this->settings->get('allow_negative_stock', false)) {
                throw new RuntimeException('Insufficient stock for variant '.$locked->sku);
            }

            $locked->update(['stock_quantity' => $after]);

            return InventoryTransaction::query()->create([
                'product_variant_id' => $locked->id,
                'inventory_location_id' => $locationId,
                'user_id' => $userId,
                'type' => $type,
                'quantity_before' => $before,
                'quantity_change' => $change,
                'quantity_after' => $after,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'reason' => $reason,
            ]);
        });
    }

    public function reserve(ProductVariant $variant, int $quantity, ?int $userId = null, ?string $referenceType = null, ?int $referenceId = null): void
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException('Reserve quantity must be positive.');
        }

        DB::transaction(function () use ($variant, $quantity, $userId, $referenceType, $referenceId) {
            $locked = ProductVariant::query()->whereKey($variant->id)->lockForUpdate()->firstOrFail();
            $available = (int) $locked->stock_quantity - (int) $locked->reserved_quantity;

            if ($available < $quantity && ! $this->settings->get('allow_negative_stock', false)) {
                throw new RuntimeException('Insufficient available stock to reserve for '.$locked->sku);
            }

            $locked->update([
                'reserved_quantity' => (int) $locked->reserved_quantity + $quantity,
            ]);

            InventoryTransaction::query()->create([
                'product_variant_id' => $locked->id,
                'user_id' => $userId,
                'type' => 'reserve',
                'quantity_before' => (int) $locked->stock_quantity,
                'quantity_change' => 0,
                'quantity_after' => (int) $locked->stock_quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'reason' => 'Reserved '.$quantity,
            ]);
        });
    }

    public function releaseReservation(ProductVariant $variant, int $quantity, ?int $userId = null, ?string $referenceType = null, ?int $referenceId = null): void
    {
        DB::transaction(function () use ($variant, $quantity, $userId, $referenceType, $referenceId) {
            $locked = ProductVariant::query()->whereKey($variant->id)->lockForUpdate()->firstOrFail();
            $newReserved = max(0, (int) $locked->reserved_quantity - $quantity);
            $locked->update(['reserved_quantity' => $newReserved]);

            InventoryTransaction::query()->create([
                'product_variant_id' => $locked->id,
                'user_id' => $userId,
                'type' => 'release',
                'quantity_before' => (int) $locked->stock_quantity,
                'quantity_change' => 0,
                'quantity_after' => (int) $locked->stock_quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'reason' => 'Released reservation '.$quantity,
            ]);
        });
    }

    public function deductForSale(ProductVariant $variant, int $quantity, ?int $userId = null, ?string $referenceType = null, ?int $referenceId = null): InventoryTransaction
    {
        return DB::transaction(function () use ($variant, $quantity, $userId, $referenceType, $referenceId) {
            $locked = ProductVariant::query()->whereKey($variant->id)->lockForUpdate()->firstOrFail();
            $before = (int) $locked->stock_quantity;
            $after = $before - $quantity;

            if ($after < 0 && ! $this->settings->get('allow_negative_stock', false)) {
                throw new RuntimeException('Insufficient stock to sell '.$locked->sku);
            }

            $locked->update([
                'stock_quantity' => $after,
                'reserved_quantity' => max(0, (int) $locked->reserved_quantity - $quantity),
            ]);

            return InventoryTransaction::query()->create([
                'product_variant_id' => $locked->id,
                'user_id' => $userId,
                'type' => 'sell',
                'quantity_before' => $before,
                'quantity_change' => -$quantity,
                'quantity_after' => $after,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'reason' => 'Sold '.$quantity,
            ]);
        });
    }

    public function restoreAfterCancellation(ProductVariant $variant, int $quantity, ?int $userId = null, ?string $referenceType = null, ?int $referenceId = null): InventoryTransaction
    {
        return $this->adjust(
            $variant,
            $quantity,
            'return',
            $userId,
            'Restored after cancellation',
            $referenceType,
            $referenceId,
        );
    }
}
