<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Services\PaymentService;
use App\Http\Controllers\Controller;
use App\Support\MediaUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('payments.manage');

        $payments = Payment::query()
            ->with(['order', 'paymentMethod', 'recordedBy'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $payments,
            'filters' => $request->only(['status']),
        ]);
    }

    public function store(Request $request, Order $order, PaymentService $payments, MediaUploader $media): RedirectResponse
    {
        $this->authorize('payments.manage');

        $validated = $request->validate([
            'payment_method_id' => ['nullable', 'integer', 'exists:payment_methods,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'receipt' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $media->image($request->file('receipt'), 'payment-receipts');
        }

        $payments->record($order, $validated, $request->user());

        return back()->with('success', 'Payment recorded.');
    }

    public function verify(Request $request, Payment $payment, PaymentService $payments): RedirectResponse
    {
        $this->authorize('payments.verify');

        $payments->verify($payment, $request->user());

        return back()->with('success', 'Payment verified.');
    }
}
