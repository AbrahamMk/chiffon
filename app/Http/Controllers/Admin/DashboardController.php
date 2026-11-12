<?php

namespace App\Http\Controllers\Admin;

use App\Domains\Catalog\Models\ProductVariant;
use App\Domains\Customers\Models\Customer;
use App\Domains\CustomOrders\Models\CustomOrder;
use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Payment;
use App\Domains\Settings\Services\SettingsService;
use App\Http\Controllers\Controller;
use App\Support\Reports\SalesReportQuery;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(SettingsService $settings, SalesReportQuery $sales): Response
    {
        $this->authorize('dashboard.view');

        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        $todaySales = max(
            $sales->verifiedTotal($today, $today),
            $sales->paidOrdersTotal($today, $today),
        );

        $monthSales = max(
            $sales->verifiedTotal($monthStart, $today),
            $sales->paidOrdersTotal($monthStart, $today),
        );

        $threshold = (int) $settings->get('low_stock_threshold', 5);

        $lowStock = ProductVariant::query()
            ->where('is_active', true)
            ->where('stock_quantity', '<=', $threshold)
            ->count();

        $customInProduction = CustomOrder::query()
            ->whereIn('status', ['in_production', 'approved', 'quoted'])
            ->count();

        $recentOrders = Order::query()
            ->latest()
            ->limit(8)
            ->get(['id', 'order_number', 'customer_name', 'status', 'payment_status', 'grand_total', 'created_at']);

        $recentPayments = Payment::query()
            ->with('order:id,order_number')
            ->latest()
            ->limit(8)
            ->get(['id', 'payment_reference', 'order_id', 'amount', 'status', 'created_at']);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'today_sales' => number_format($todaySales, 2, '.', ''),
                'month_sales' => number_format($monthSales, 2, '.', ''),
                'orders_count' => Order::query()->count(),
                'customers_count' => Customer::query()->count(),
                'low_stock_count' => $lowStock,
                'custom_orders_in_production' => $customInProduction,
            ],
            'recentOrders' => $recentOrders,
            'recentPayments' => $recentPayments,
        ]);
    }
}
