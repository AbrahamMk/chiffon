<?php

namespace App\Http\Controllers\Admin\CustomOrder;

use App\Domains\CustomOrders\Models\CustomOrder;
use App\Domains\CustomOrders\Models\Quotation;
use App\Http\Controllers\Controller;
use App\Support\MediaUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CustomOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('custom_orders.manage');

        $orders = CustomOrder::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/CustomOrders/Index', [
            'customOrders' => $orders,
            'filters' => $request->only(['status']),
        ]);
    }

    public function show(CustomOrder $customOrder, MediaUploader $media): Response
    {
        $this->authorize('custom_orders.manage');

        $customOrder->load(['measurements', 'images', 'quotations.creator', 'productionJob', 'customer']);

        return Inertia::render('Admin/CustomOrders/Show', [
            'customOrder' => $customOrder,
            'imageUrls' => $customOrder->images->mapWithKeys(fn ($img) => [
                $img->id => $media->url($img->path),
            ]),
        ]);
    }

    public function updateStatus(Request $request, CustomOrder $customOrder): RedirectResponse
    {
        $this->authorize('custom_orders.manage');

        $validated = $request->validate([
            'status' => ['required', 'string', 'max:50'],
        ]);

        $customOrder->update(['status' => $validated['status']]);

        return back()->with('success', 'Status updated.');
    }

    public function storeQuotation(Request $request, CustomOrder $customOrder): RedirectResponse
    {
        $this->authorize('custom_orders.manage');

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'deposit_required' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $reference = 'QT-'.now()->format('ymd').'-'.Str::upper(Str::random(4));

        Quotation::query()->create([
            'custom_order_id' => $customOrder->id,
            'created_by' => $request->user()->id,
            'reference' => $reference,
            'amount' => $validated['amount'],
            'deposit_required' => $validated['deposit_required'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $customOrder->update([
            'status' => 'quoted',
            'quoted_amount' => $validated['amount'],
            'deposit_amount' => $validated['deposit_required'] ?? 0,
        ]);

        return back()->with('success', 'Quotation saved and sent.');
    }
}
