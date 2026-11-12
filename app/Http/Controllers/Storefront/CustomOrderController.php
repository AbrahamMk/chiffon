<?php

namespace App\Http\Controllers\Storefront;

use App\Domains\CustomOrders\Models\CustomOrder;
use App\Domains\CustomOrders\Services\CustomOrderService;
use App\Domains\Settings\Services\SettingsService;
use App\Http\Controllers\Controller;
use App\Support\MediaUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomOrderController extends Controller
{
    public function create(SettingsService $settings): Response
    {
        if (! $settings->get('custom_orders_enabled', true)) {
            abort(403, 'Custom orders are not available.');
        }

        return Inertia::render('Storefront/CustomOrders/Create');
    }

    public function store(
        Request $request,
        CustomOrderService $customOrders,
        SettingsService $settings,
    ): RedirectResponse {
        if (! $settings->get('custom_orders_enabled', true)) {
            abort(403, 'Custom orders are not available.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'clothing_type' => ['nullable', 'string', 'max:120'],
            'fabric_preference' => ['nullable', 'string', 'max:120'],
            'color_preference' => ['nullable', 'string', 'max:120'],
            'design_description' => ['nullable', 'string', 'max:5000'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:50'],
            'required_date' => ['nullable', 'date', 'after:today'],
            'additional_instructions' => ['nullable', 'string', 'max:2000'],
            'measurements' => ['nullable', 'array'],
            'measurements.*.name' => ['required_with:measurements', 'string', 'max:80'],
            'measurements.*.value' => ['nullable', 'string', 'max:40'],
            'measurements.*.unit' => ['nullable', 'string', 'max:20'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'max:5120'],
        ]);

        $images = $request->file('images', []);

        $order = $customOrders->create($validated, $request->user(), $images);

        return redirect()
            ->route('custom-orders.show', $order->reference)
            ->with('success', 'Your custom order request was submitted.');
    }

    public function show(string $reference, MediaUploader $media): Response
    {
        $order = CustomOrder::query()
            ->where('reference', $reference)
            ->with(['measurements', 'images'])
            ->firstOrFail();

        return Inertia::render('Storefront/CustomOrders/Show', [
            'customOrder' => [
                'reference' => $order->reference,
                'status' => $order->status,
                'customer_name' => $order->customer_name,
                'clothing_type' => $order->clothing_type,
                'design_description' => $order->design_description,
                'required_date' => $order->required_date?->toDateString(),
                'quoted_amount' => $order->quoted_amount,
                'measurements' => $order->measurements,
                'images' => $order->images->map(fn ($img) => [
                    'url' => $media->url($img->path),
                    'caption' => $img->caption,
                ]),
            ],
        ]);
    }
}
