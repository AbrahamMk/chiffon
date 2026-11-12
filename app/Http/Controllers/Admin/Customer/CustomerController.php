<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Domains\Customers\Models\Customer;
use App\Domains\Orders\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('customers.manage');

        $customers = Customer::query()
            ->with('user')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->string('q')->toString();
                $q->where(function ($inner) use ($term) {
                    $inner->where('full_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('customer_number', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers,
            'filters' => $request->only(['q']),
        ]);
    }

    public function show(Customer $customer): Response
    {
        $this->authorize('customers.manage');

        $orders = Order::query()
            ->where('customer_id', $customer->id)
            ->latest()
            ->limit(20)
            ->get();

        $customer->load(['user', 'addresses']);

        return Inertia::render('Admin/Customers/Show', [
            'customer' => $customer,
            'recentOrders' => $orders,
        ]);
    }
}
