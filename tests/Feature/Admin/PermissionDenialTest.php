<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\BusinessSettingsSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionDenialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(BusinessSettingsSeeder::class);
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('customer');

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_salesperson_cannot_manage_settings(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('salesperson');

        $this->actingAs($user)
            ->get(route('admin.settings.brand.edit'))
            ->assertForbidden();
    }

    public function test_owner_can_view_admin_dashboard(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('owner');

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }
}
