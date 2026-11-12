<?php

namespace App\Support\Reports;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalesReportQuery
{
    /**
     * @return Collection<int, array<int, mixed>>
     */
    public function rows(?string $from = null, ?string $to = null): Collection
    {
        $start = $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth();
        $end = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();

        return Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('created_at')
            ->get(['order_number', 'customer_name', 'status', 'payment_status', 'grand_total', 'created_at'])
            ->map(fn (Order $order) => [
                $order->order_number,
                $order->customer_name,
                $order->status,
                $order->payment_status,
                $order->grand_total,
                $order->created_at?->toDateTimeString(),
            ]);
    }

    public function verifiedTotal(?string $from = null, ?string $to = null): float
    {
        $start = $from ? Carbon::parse($from)->startOfDay() : now()->startOfDay();
        $end = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();

        return (float) Payment::query()
            ->where('status', 'verified')
            ->whereBetween('verified_at', [$start, $end])
            ->sum('amount');
    }

    public function paidOrdersTotal(?string $from = null, ?string $to = null): float
    {
        $start = $from ? Carbon::parse($from)->startOfDay() : now()->startOfDay();
        $end = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();

        return (float) Order::query()
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->sum('grand_total');
    }
}
