<?php

namespace App\Domains\CustomOrders\Services;

use App\Domains\CustomOrders\Models\CustomOrder;
use App\Domains\CustomOrders\Models\CustomOrderImage;
use App\Domains\CustomOrders\Models\CustomOrderMeasurement;
use App\Models\User;
use App\Support\MediaUploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomOrderService
{
    public function __construct(private MediaUploader $media) {}

    /**
     * @param  array<string, mixed>  $payload
     * @param  list<UploadedFile>|null  $images
     */
    public function create(array $payload, ?User $user = null, ?array $images = null): CustomOrder
    {
        return DB::transaction(function () use ($payload, $user, $images) {
            $order = CustomOrder::query()->create([
                'reference' => $this->generateReference(),
                'customer_id' => $user?->customer?->id ?? ($payload['customer_id'] ?? null),
                'user_id' => $user?->id,
                'customer_name' => $payload['customer_name'],
                'customer_phone' => $payload['customer_phone'],
                'customer_email' => $payload['customer_email'] ?? null,
                'clothing_type' => $payload['clothing_type'] ?? null,
                'fabric_preference' => $payload['fabric_preference'] ?? null,
                'color_preference' => $payload['color_preference'] ?? null,
                'design_description' => $payload['design_description'] ?? null,
                'quantity' => (int) ($payload['quantity'] ?? 1),
                'required_date' => $payload['required_date'] ?? null,
                'additional_instructions' => $payload['additional_instructions'] ?? null,
                'status' => 'submitted',
            ]);

            foreach ($payload['measurements'] ?? [] as $measurement) {
                if (empty($measurement['name'])) {
                    continue;
                }

                CustomOrderMeasurement::query()->create([
                    'custom_order_id' => $order->id,
                    'name' => $measurement['name'],
                    'value' => $measurement['value'] ?? null,
                    'unit' => $measurement['unit'] ?? 'cm',
                ]);
            }

            foreach ($images ?? [] as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $path = $this->media->image($file, 'custom-orders');

                CustomOrderImage::query()->create([
                    'custom_order_id' => $order->id,
                    'path' => $path,
                ]);
            }

            return $order->fresh(['measurements', 'images']);
        });
    }

    private function generateReference(): string
    {
        do {
            $reference = 'CUS-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (CustomOrder::query()->where('reference', $reference)->exists());

        return $reference;
    }
}
