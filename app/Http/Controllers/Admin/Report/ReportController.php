<?php

namespace App\Http\Controllers\Admin\Report;

use App\Domains\Catalog\Models\ProductVariant;
use App\Domains\Payments\Models\Payment;
use App\Domains\Settings\Services\SettingsService;
use App\Exports\SalesReportExport;
use App\Http\Controllers\Controller;
use App\Support\Reports\SalesReportQuery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function sales(Request $request, SalesReportQuery $sales): Response
    {
        $this->authorize('reports.view');

        $from = $request->input('from');
        $to = $request->input('to');

        return Inertia::render('Admin/Reports/Sales', [
            'filters' => ['from' => $from, 'to' => $to],
            'summary' => [
                'verified_payments' => number_format($sales->verifiedTotal($from, $to), 2, '.', ''),
                'paid_orders' => number_format($sales->paidOrdersTotal($from, $to), 2, '.', ''),
                'row_count' => $sales->rows($from, $to)->count(),
            ],
            'rows' => $sales->rows($from, $to)->take(100)->values(),
        ]);
    }

    public function inventory(Request $request, SettingsService $settings): Response
    {
        $this->authorize('reports.view');

        $threshold = (int) $settings->get('low_stock_threshold', 5);

        $variants = ProductVariant::query()
            ->with('product')
            ->where('is_active', true)
            ->orderBy('stock_quantity')
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('Admin/Reports/Inventory', [
            'variants' => $variants,
            'lowStockThreshold' => $threshold,
        ]);
    }

    public function payments(Request $request): Response
    {
        $this->authorize('reports.view');

        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->endOfDay();

        $byStatus = Payment::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('status')
            ->get();

        return Inertia::render('Admin/Reports/Payments', [
            'filters' => ['from' => $from, 'to' => $to],
            'byStatus' => $byStatus,
        ]);
    }

    public function exportSales(Request $request, SalesReportQuery $sales): BinaryFileResponse
    {
        $this->authorize('reports.view');

        $from = $request->input('from');
        $to = $request->input('to');
        $filename = 'sales-report-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(new SalesReportExport($sales->rows($from, $to)), $filename);
    }
}
