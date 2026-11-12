<?php

namespace App\Http\Controllers\Admin\Delivery;

use App\Domains\Delivery\Models\Delivery;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('deliveries.manage');

        $deliveries = Delivery::query()
            ->with(['order', 'zone', 'assignedTo'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Deliveries/Index', [
            'deliveries' => $deliveries,
            'filters' => $request->only(['status']),
        ]);
    }

    public function show(Delivery $delivery): Response
    {
        $this->authorize('deliveries.manage');

        $delivery->load(['order.items', 'zone', 'assignedTo']);

        return Inertia::render('Admin/Deliveries/Show', [
            'delivery' => $delivery,
        ]);
    }

    public function assign(Request $request, Delivery $delivery): RedirectResponse
    {
        $this->authorize('deliveries.manage');

        $validated = $request->validate([
            'assigned_to' => ['required', 'integer', 'exists:users,id'],
            'scheduled_date' => ['nullable', 'date'],
        ]);

        $delivery->update([
            'assigned_to' => $validated['assigned_to'],
            'scheduled_date' => $validated['scheduled_date'] ?? $delivery->scheduled_date,
        ]);

        return back()->with('success', 'Delivery assigned.');
    }

    public function updateStatus(Request $request, Delivery $delivery): RedirectResponse
    {
        $this->authorize('deliveries.update');

        $validated = $request->validate([
            'status' => ['required', 'string', 'max:50'],
            'failed_reason' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $updates = [
            'status' => $validated['status'],
            'failed_reason' => $validated['failed_reason'] ?? null,
            'notes' => $validated['notes'] ?? $delivery->notes,
        ];

        if ($validated['status'] === 'delivered') {
            $updates['delivered_at'] = now();
        }

        $delivery->update($updates);

        if ($validated['status'] === 'delivered' && $delivery->order) {
            $delivery->order->update(['status' => 'delivered']);
        }

        return back()->with('success', 'Delivery status updated.');
    }
}
