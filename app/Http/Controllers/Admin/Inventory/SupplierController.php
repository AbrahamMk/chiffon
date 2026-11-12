<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Domains\Inventory\Models\Supplier;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inventory\StoreSupplierRequest;
use App\Http\Requests\Admin\Inventory\UpdateSupplierRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    public function index(): Response
    {
        $this->authorize('inventory.manage');

        $suppliers = Supplier::query()
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Inventory/Suppliers/Index', [
            'suppliers' => $suppliers,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('inventory.manage');

        return Inertia::render('Admin/Inventory/Suppliers/Form', [
            'supplier' => null,
        ]);
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $this->authorize('inventory.manage');

        Supplier::query()->create($request->validated());

        return redirect()
            ->route('admin.inventory.suppliers.index')
            ->with('success', 'Supplier created.');
    }

    public function edit(Supplier $supplier): Response
    {
        $this->authorize('inventory.manage');

        return Inertia::render('Admin/Inventory/Suppliers/Form', [
            'supplier' => $supplier,
        ]);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('inventory.manage');

        $supplier->update($request->validated());

        return redirect()
            ->route('admin.inventory.suppliers.index')
            ->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->authorize('inventory.manage');

        $supplier->delete();

        return redirect()
            ->route('admin.inventory.suppliers.index')
            ->with('success', 'Supplier deleted.');
    }
}
