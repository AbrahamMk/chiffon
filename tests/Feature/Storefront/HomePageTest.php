<?php

namespace Tests\Feature\Storefront;

use Database\Seeders\BusinessSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders(): void
    {
        $this->seed(BusinessSettingsSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Storefront/Home'));
    }
}
