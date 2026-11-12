<?php

namespace Database\Seeders;

use App\Domains\Customers\Actions\CreateCustomerFromUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Demo accounts for local development only.
     */
    public function run(): void
    {
        $owner = User::query()->updateOrCreate(
            ['email' => 'owner@aster.test'],
            [
                'name' => 'Aster Owner',
                'phone' => null,
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $owner->syncRoles(['owner']);

        $customerUser = User::query()->updateOrCreate(
            ['email' => 'customer@aster.test'],
            [
                'name' => 'Demo Customer',
                'phone' => null,
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        app(CreateCustomerFromUser::class)->handle($customerUser);
    }
}
