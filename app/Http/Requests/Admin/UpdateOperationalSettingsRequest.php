<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOperationalSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('settings.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'custom_orders_enabled' => ['sometimes', 'boolean'],
            'customer_registration_enabled' => ['sometimes', 'boolean'],
            'guest_checkout_enabled' => ['sometimes', 'boolean'],
            'allow_negative_stock' => ['sometimes', 'boolean'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:10000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'custom_orders_enabled' => $this->boolean('custom_orders_enabled'),
            'customer_registration_enabled' => $this->boolean('customer_registration_enabled'),
            'guest_checkout_enabled' => $this->boolean('guest_checkout_enabled'),
            'allow_negative_stock' => $this->boolean('allow_negative_stock'),
        ]);
    }
}
