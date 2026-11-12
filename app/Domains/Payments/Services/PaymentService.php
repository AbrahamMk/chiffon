<?php

namespace App\Domains\Payments\Services;

use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Services\OrderService;
use App\Domains\Payments\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentService
{
    public function __construct(private OrderService $orders) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function record(Order $order, array $data, ?User $actor = null): Payment
    {
        return DB::transaction(function () use ($order, $data, $actor) {
            $payment = Payment::query()->create([
                'payment_reference' => 'PAY-'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
                'order_id' => $order->id,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'recorded_by' => $actor?->id,
                'amount' => $data['amount'],
                'status' => 'pending',
                'paid_at' => $data['paid_at'] ?? now(),
                'receipt_path' => $data['receipt_path'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            return $payment;
        });
    }

    public function verify(Payment $payment, User $verifier): Payment
    {
        if ($payment->status === 'verified') {
            return $payment;
        }

        return DB::transaction(function () use ($payment, $verifier) {
            $payment->update([
                'status' => 'verified',
                'verified_by' => $verifier->id,
                'verified_at' => now(),
            ]);

            $this->orders->refreshPaymentStatus($payment->order);

            return $payment->fresh();
        });
    }

    public function refund(Payment $payment, User $actor): Payment
    {
        if ($payment->status !== 'verified') {
            throw new RuntimeException('Only verified payments can be refunded.');
        }

        return DB::transaction(function () use ($payment, $actor) {
            $payment->update([
                'status' => 'refunded',
                'notes' => trim(($payment->notes ? $payment->notes."\n" : '').'Refunded by user #'.$actor->id),
            ]);

            $this->orders->refreshPaymentStatus($payment->order);

            return $payment->fresh();
        });
    }
}
