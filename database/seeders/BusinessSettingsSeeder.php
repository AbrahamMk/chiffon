<?php

namespace Database\Seeders;

use App\Domains\Settings\Services\SettingsService;
use Illuminate\Database\Seeder;

class BusinessSettingsSeeder extends Seeder
{
    public function run(): void
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        $settings->setMany([
            'brand_name' => 'Aster',
            'brand_tagline' => 'Ethiopian fashion, modern elegance',
            'brand_description' => 'A configurable fashion brand platform. Update this description in Settings.',
            'brand_logo' => null,
            'brand_favicon' => null,
            'brand_primary_color' => '#7A1F2B',
            'brand_accent_color' => '#C6A75E',
            'currency' => 'ETB',
            'timezone' => 'Africa/Addis_Ababa',
            'contact_email' => null,
            'contact_phone' => null,
            'business_address' => null,
            'social_instagram' => null,
            'social_tiktok' => null,
            'social_facebook' => null,
            'social_youtube' => null,
        ], 'brand');

        $settings->setMany([
            'custom_orders_enabled' => true,
            'customer_registration_enabled' => true,
            'guest_checkout_enabled' => true,
            'allow_negative_stock' => false,
            'low_stock_threshold' => 5,
        ], 'operational');
    }
}
