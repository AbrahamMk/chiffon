<?php

namespace App\Domains\Customers\Actions;

use App\Domains\Customers\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateCustomerFromUser
{
    public function handle(User $user, ?string $phone = null): Customer
    {
        return DB::transaction(function () use ($user, $phone) {
            if (! $user->hasRole('customer')) {
                $user->assignRole('customer');
            }

            return Customer::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'customer_number' => $this->generateCustomerNumber(),
                    'full_name' => $user->name,
                    'phone' => $phone ?? $user->phone,
                    'email' => $user->email,
                    'status' => 'active',
                ]
            );
        });
    }

    private function generateCustomerNumber(): string
    {
        do {
            $number = 'CUS-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (Customer::query()->where('customer_number', $number)->exists());

        return $number;
    }
}
