<?php

namespace App\Http\Controllers\Admin\Production;

use App\Domains\Production\Models\ProductionJob;
use App\Domains\Production\Models\ProductionJobStage;
use App\Domains\Production\Services\ProductionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class ProductionJobController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('production.manage');

        $jobs = ProductionJob::query()
            ->with(['assignedTo', 'customOrder', 'order'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Production/Index', [
            'jobs' => $jobs,
            'filters' => $request->only(['status']),
        ]);
    }

    public function show(ProductionJob $productionJob): Response
    {
        $this->authorize('production.manage');

        $productionJob->load(['jobStages.productionStage', 'assignedTo', 'customOrder', 'order']);

        return Inertia::render('Admin/Production/Show', [
            'job' => $productionJob,
        ]);
    }

    public function store(Request $request, ProductionService $production): RedirectResponse
    {
        $this->authorize('production.manage');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'custom_order_id' => ['nullable', 'integer', 'exists:custom_orders,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'expected_completion_date' => ['nullable', 'date'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $job = $production->createJob($validated, $request->user());

        return redirect()
            ->route('admin.production.show', $job)
            ->with('success', 'Production job created.');
    }

    public function updateStage(Request $request, ProductionJobStage $stage, ProductionService $production): RedirectResponse
    {
        $this->authorize('production.update');

        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $production->updateStage($stage, $validated['status'], $validated['notes'] ?? null);
        } catch (RuntimeException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('success', 'Stage updated.');
    }
}
