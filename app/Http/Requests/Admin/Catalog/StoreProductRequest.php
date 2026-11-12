<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('products.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'category' => ['nullable', 'integer', Rule::exists('product_categories', 'id')],
            'description' => ['nullable', 'string', 'max:10000'],
            'fabric_info' => ['nullable', 'string', 'max:5000'],
            'care_instructions' => ['nullable', 'string', 'max:5000'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', Rule::in(['draft', 'active', 'archived'])],
            'is_featured' => ['sometimes', 'boolean'],
            'variants' => ['nullable', 'array'],
            'variants.*.sku' => ['required', 'string', 'max:80', 'distinct', Rule::unique('product_variants', 'sku')],
            'variants.*.name' => ['nullable', 'string', 'max:120'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.stock_quantity' => ['nullable', 'integer', 'min:0'],
            'variants.*.is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'category' => $this->input('category') ?: $this->input('product_category_id'),
        ]);

        if ($this->has('variants') && is_array($this->input('variants'))) {
            $variants = collect($this->input('variants'))
                ->map(function (array $variant): array {
                    $variant['is_active'] = filter_var($variant['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

                    return $variant;
                })
                ->all();

            $this->merge(['variants' => $variants]);
        }
    }
}
