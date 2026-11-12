<?php

namespace App\Support\Catalog;

use App\Domains\Catalog\Models\Product;
use App\Support\MediaUploader;

class ProductPresenter
{
    public function __construct(private MediaUploader $media) {}

    /**
     * @return array<string, mixed>
     */
    public function card(Product $product): array
    {
        $product->loadMissing(['images', 'category', 'variants' => fn ($q) => $q->where('is_active', true)]);

        $variants = $product->variants;
        $minPrice = $variants->min('price') ?? $product->base_price;
        $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first();

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'category' => $product->category?->only(['id', 'name', 'slug']),
            'min_price' => $minPrice,
            'compare_at_price' => $product->compare_at_price,
            'is_featured' => $product->is_featured,
            'image' => $this->media->url($primary?->path),
            'has_variants' => $variants->count() > 1,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function detail(Product $product): array
    {
        $product->load([
            'category',
            'images',
            'variants' => fn ($q) => $q->where('is_active', true)->orderBy('price'),
            'variants.attributeValues.attribute',
        ]);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'fabric_info' => $product->fabric_info,
            'care_instructions' => $product->care_instructions,
            'base_price' => $product->base_price,
            'compare_at_price' => $product->compare_at_price,
            'category' => $product->category?->only(['id', 'name', 'slug']),
            'images' => $product->images->sortBy('sort_order')->map(fn ($img) => [
                'id' => $img->id,
                'url' => $this->media->url($img->path),
                'alt_text' => $img->alt_text,
                'is_primary' => $img->is_primary,
            ])->values(),
            'variants' => $product->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'name' => $variant->name,
                'price' => $variant->price,
                'compare_at_price' => $variant->compare_at_price,
                'stock_quantity' => $variant->stock_quantity,
                'attributes' => $variant->attributeValues->map(fn ($av) => [
                    'attribute' => $av->attribute?->name,
                    'value' => $av->value,
                ])->values(),
            ])->values(),
        ];
    }
}
