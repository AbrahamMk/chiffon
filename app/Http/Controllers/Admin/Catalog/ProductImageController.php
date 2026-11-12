<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductImage;
use App\Http\Controllers\Controller;
use App\Support\MediaUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductImageController extends Controller
{
    public function store(Request $request, Product $product, MediaUploader $media): RedirectResponse
    {
        $this->authorize('products.manage');

        $validated = $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:8'],
            'images.*' => ['required', 'image', 'max:5120'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($product, $validated, $media, $request) {
            $hasPrimary = $product->images()->where('is_primary', true)->exists();
            $sort = (int) $product->images()->max('sort_order');

            foreach ($request->file('images', []) as $index => $file) {
                $path = $media->image($file, 'products/'.$product->id);
                $sort++;

                $product->images()->create([
                    'path' => $path,
                    'alt_text' => $validated['alt_text'] ?? $product->name,
                    'sort_order' => $sort,
                    'is_primary' => ! $hasPrimary && $index === 0,
                ]);
            }
        });

        return back()->with('success', 'Images uploaded.');
    }

    public function destroy(Product $product, ProductImage $image, MediaUploader $media): RedirectResponse
    {
        $this->authorize('products.manage');
        abort_unless($image->product_id === $product->id, 404);

        $wasPrimary = $image->is_primary;
        $media->delete($image->path);
        $image->delete();

        if ($wasPrimary) {
            $next = $product->images()->orderBy('sort_order')->first();
            $next?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Image removed.');
    }

    public function makePrimary(Product $product, ProductImage $image): RedirectResponse
    {
        $this->authorize('products.manage');
        abort_unless($image->product_id === $product->id, 404);

        DB::transaction(function () use ($product, $image) {
            $product->images()->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        return back()->with('success', 'Primary image updated.');
    }
}
