<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Domains\Settings\Services\SettingsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBrandSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BrandSettingsController extends Controller
{
    public function edit(SettingsService $settings): Response
    {
        $this->authorize('settings.manage');

        return Inertia::render('Admin/Settings/Brand', [
            'settings' => [
                'brand_name' => $settings->get('brand_name', 'Aster'),
                'brand_tagline' => $settings->get('brand_tagline', ''),
                'brand_description' => $settings->get('brand_description', ''),
                'brand_primary_color' => $settings->get('brand_primary_color', '#7A1F2B'),
                'brand_accent_color' => $settings->get('brand_accent_color', '#C6A75E'),
                'contact_email' => $settings->get('contact_email'),
                'contact_phone' => $settings->get('contact_phone'),
                'business_address' => $settings->get('business_address'),
                'social_instagram' => $settings->get('social_instagram'),
                'social_tiktok' => $settings->get('social_tiktok'),
                'social_facebook' => $settings->get('social_facebook'),
                'social_youtube' => $settings->get('social_youtube'),
                'currency' => $settings->get('currency', 'ETB'),
                'timezone' => $settings->get('timezone', 'Africa/Addis_Ababa'),
            ],
        ]);
    }

    public function update(UpdateBrandSettingsRequest $request, SettingsService $settings): RedirectResponse
    {
        $this->authorize('settings.manage');

        $settings->setMany($request->validated(), 'brand');

        return back()->with('success', 'Brand settings updated.');
    }
}
