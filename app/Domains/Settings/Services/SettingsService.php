<?php

namespace App\Domains\Settings\Services;

use App\Domains\Settings\Models\BusinessSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public const CACHE_KEY = 'business_settings.all';

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return BusinessSetting::query()
                ->get()
                ->mapWithKeys(fn (BusinessSetting $setting) => [
                    $setting->key => $this->castValue($setting->value, $setting->type),
                ])
                ->all();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function setMany(array $values, string $group = 'general'): void
    {
        foreach ($values as $key => $value) {
            $type = match (true) {
                is_bool($value) => 'boolean',
                is_int($value) => 'integer',
                is_array($value) => 'json',
                default => 'string',
            };

            $stored = match ($type) {
                'boolean' => $value ? '1' : '0',
                'json' => json_encode($value),
                default => (string) $value,
            };

            BusinessSetting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'group' => $group,
                    'value' => $stored,
                    'type' => $type,
                ]
            );
        }

        $this->forgetCache();
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Brand props shared with Inertia.
     *
     * @return array<string, mixed>
     */
    public function brandPayload(): array
    {
        $settings = $this->all();

        return [
            'name' => $settings['brand_name'] ?? config('app.name', 'Aster'),
            'tagline' => $settings['brand_tagline'] ?? '',
            'description' => $settings['brand_description'] ?? '',
            'logo' => $settings['brand_logo'] ?? null,
            'favicon' => $settings['brand_favicon'] ?? null,
            'primary_color' => $settings['brand_primary_color'] ?? '#7A1F2B',
            'accent_color' => $settings['brand_accent_color'] ?? '#C6A75E',
            'currency' => $settings['currency'] ?? 'ETB',
            'timezone' => $settings['timezone'] ?? 'Africa/Addis_Ababa',
            'contact_email' => $settings['contact_email'] ?? null,
            'contact_phone' => $settings['contact_phone'] ?? null,
            'social' => [
                'instagram' => $settings['social_instagram'] ?? null,
                'tiktok' => $settings['social_tiktok'] ?? null,
                'facebook' => $settings['social_facebook'] ?? null,
                'youtube' => $settings['social_youtube'] ?? null,
            ],
            'features' => [
                'custom_orders_enabled' => (bool) ($settings['custom_orders_enabled'] ?? true),
                'customer_registration_enabled' => (bool) ($settings['customer_registration_enabled'] ?? true),
                'guest_checkout_enabled' => (bool) ($settings['guest_checkout_enabled'] ?? true),
                'allow_negative_stock' => (bool) ($settings['allow_negative_stock'] ?? false),
            ],
        ];
    }

    private function castValue(?string $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => $value === '1' || $value === 'true',
            'integer' => (int) $value,
            'json' => json_decode($value ?? 'null', true),
            default => $value,
        };
    }
}
