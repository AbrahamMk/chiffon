<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Domains\Settings\Services\SettingsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOperationalSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OperationalSettingsController extends Controller
{
    public function edit(SettingsService $settings): Response
    {
        $this->authorize('settings.manage');

        return Inertia::render('Admin/Settings/Operational', [
            'settings' => [
                'custom_orders_enabled' => (bool) $settings->get('custom_orders_enabled', true),
                'customer_registration_enabled' => (bool) $settings->get('customer_registration_enabled', true),
                'guest_checkout_enabled' => (bool) $settings->get('guest_checkout_enabled', true),
                'allow_negative_stock' => (bool) $settings->get('allow_negative_stock', false),
                'low_stock_threshold' => (int) $settings->get('low_stock_threshold', 5),
            ],
        ]);
    }

    public function update(UpdateOperationalSettingsRequest $request, SettingsService $settings): RedirectResponse
    {
        $this->authorize('settings.manage');

        $data = $request->validated();

        $settings->setMany([
            'custom_orders_enabled' => (bool) ($data['custom_orders_enabled'] ?? false),
            'customer_registration_enabled' => (bool) ($data['customer_registration_enabled'] ?? false),
            'guest_checkout_enabled' => (bool) ($data['guest_checkout_enabled'] ?? false),
            'allow_negative_stock' => (bool) ($data['allow_negative_stock'] ?? false),
            'low_stock_threshold' => (int) $data['low_stock_threshold'],
        ], 'operational');

        return back()->with('success', 'Operational settings updated.');
    }
}
