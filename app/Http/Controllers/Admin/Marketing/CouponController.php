<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Domains\Marketing\Models\Coupon;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CouponController extends Controller
{
    public function index(): Response
    {
        $this->authorize('marketing.manage');

        return Inertia::render('Admin/Marketing/Coupons/Index', [
            'coupons' => Coupon::query()->latest()->paginate(20),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('marketing.manage');

        return Inertia::render('Admin/Marketing/Coupons/Form', [
            'coupon' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('marketing.manage');

        $validated = $this->validated($request);
        Coupon::query()->create($validated);

        return redirect()->route('admin.marketing.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon): Response
    {
        $this->authorize('marketing.manage');

        return Inertia::render('Admin/Marketing/Coupons/Form', [
            'coupon' => $coupon,
        ]);
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $this->authorize('marketing.manage');

        $coupon->update($this->validated($request));

        return redirect()->route('admin.marketing.coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $this->authorize('marketing.manage');

        $coupon->delete();

        return redirect()->route('admin.marketing.coupons.index')->with('success', 'Coupon deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
