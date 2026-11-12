<?php

namespace Tests\Feature\Admin;

use App\Domains\Settings\Services\SettingsService;
use App\Models\User;
use Database\Seeders\BusinessSettingsSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(BusinessSettingsSeeder::class);
    }

    public function test_owner_can_update_brand_settings(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('owner');

        $this->actingAs($user)
            ->put(route('admin.settings.brand.update'), [
                'brand_name' => 'Aster Atelier',
                'brand_tagline' => 'Modern Habesha',
                'brand_description' => 'Configurable description',
                'brand_primary_color' => '#7A1F2B',
                'brand_accent_color' => '#C6A75E',
                'contact_email' => null,
                'contact_phone' => null,
                'business_address' => null,
                'social_instagram' => null,
                'social_tiktok' => null,
                'social_facebook' => null,
                'social_youtube' => null,
                'currency' => 'ETB',
                'timezone' => 'Africa/Addis_Ababa',
            ])
            ->assertRedirect();

        $this->assertSame('Aster Atelier', app(SettingsService::class)->get('brand_name'));
    }

    public function test_owner_can_update_operational_settings(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('owner');

        $this->actingAs($user)
            ->put(route('admin.settings.operational.update'), [
                'custom_orders_enabled' => false,
                'customer_registration_enabled' => true,
                'guest_checkout_enabled' => false,
                'allow_negative_stock' => false,
                'low_stock_threshold' => 3,
            ])
            ->assertRedirect();

        $settings = app(SettingsService::class);
        $this->assertFalse((bool) $settings->get('custom_orders_enabled'));
        $this->assertSame(3, (int) $settings->get('low_stock_threshold'));
    }
}
