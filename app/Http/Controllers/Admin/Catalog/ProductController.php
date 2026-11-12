<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\StoreProductRequest;
use App\Http\Requests\Admin\Catalog\UpdateProductRequest;
use App\Support\MediaUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('products.manage');

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->trim()->toString();
        $categoryId = $request->integer('category');

        $media = app(MediaUploader::class);

        $products = Product::query()
            ->with(['category:id,name', 'images'])
            ->withCount('variants')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('sku', 'like', '%'.$search.'%');
                });
            })
            ->when(in_array($status, ['draft', 'active', 'archived'], true), fn ($q) => $q->where('status', $status))
            ->when($categoryId > 0, fn ($q) => $q->where('product_category_id', $categoryId))
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString()
            ->through(function (Product $product) use ($media) {
                $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'status' => $product->status,
                    'base_price' => $product->base_price,
                    'variants_count' => $product->variants_count,
                    'category' => $product->category,
                    'image_url' => $media->url($primary?->path),
                ];
            });

        return Inertia::render('Admin/Catalog/Products/Index', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'category' => $categoryId > 0 ? $categoryId : null,
            ],
            'categories' => ProductCategory::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('products.manage');

        return Inertia::render('Admin/Catalog/Products/Form', [
            'product' => null,
            'categories' => ProductCategory::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->authorize('products.manage');

        $data = $request->validated();
        $variants = $data['variants'] ?? [];

        $product = DB::transaction(function () use ($data, $variants) {
            $product = Product::query()->create([
                'product_category_id' => $data['category'] ?? null,
                'name' => $data['name'],
                'slug' => $this->uniqueProductSlug($data['name']),
                'description' => $data['description'] ?? null,
                'fabric_info' => $data['fabric_info'] ?? null,
                'care_instructions' => $data['care_instructions'] ?? null,
                'base_price' => $data['base_price'],
                'compare_at_price' => $data['compare_at_price'] ?? null,
                'status' => $data['status'],
                'is_featured' => (bool) ($data['is_featured'] ?? false),
                'published_at' => $data['status'] === 'active' ? now() : null,
            ]);

            foreach ($variants as $variant) {
                $product->variants()->create([
                    'sku' => $variant['sku'],
                    'name' => $variant['name'] ?? null,
                    'price' => $variant['price'],
                    'stock_quantity' => (int) ($variant['stock_quantity'] ?? 0),
                    'is_active' => (bool) ($variant['is_active'] ?? true),
                ]);
            }

            return $product;
        });

        return redirect()
            ->route('admin.catalog.products.edit', $product)
            ->with('success', 'Product created.');
    }

    public function show(Product $product): Response
    {
        $this->authorize('products.manage');

        $product->load([
            'category:id,name',
            'variants' => fn ($q) => $q->orderBy('sku'),
            'images' => fn ($q) => $q->orderBy('sort_order'),
        ]);

        $media = app(MediaUploader::class);

        return Inertia::render('Admin/Catalog/Products/Show', [
            'product' => [
                ...$product->toArray(),
                'images' => $product->images->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => $media->url($img->path),
                    'alt_text' => $img->alt_text,
                    'is_primary' => $img->is_primary,
                ])->values(),
            ],
        ]);
    }

    public function edit(Product $product): Response
    {
        $this->authorize('products.manage');

        $product->load([
            'category:id,name',
            'variants' => fn ($q) => $q->orderBy('sku'),
            'images' => fn ($q) => $q->orderBy('sort_order'),
        ]);

        $media = app(MediaUploader::class);

        return Inertia::render('Admin/Catalog/Products/Form', [
            'product' => [
                ...$product->toArray(),
                'images' => $product->images->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => $media->url($img->path),
                    'alt_text' => $img->alt_text,
                    'is_primary' => $img->is_primary,
                    'sort_order' => $img->sort_order,
                ])->values(),
            ],
            'categories' => ProductCategory::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('products.manage');

        $data = $request->validated();

        $product->update([
            'product_category_id' => $data['category'] ?? null,
            'name' => $data['name'],
            'slug' => $this->uniqueProductSlug($data['name'], $product->id),
            'description' => $data['description'] ?? null,
            'fabric_info' => $data['fabric_info'] ?? null,
            'care_instructions' => $data['care_instructions'] ?? null,
            'base_price' => $data['base_price'],
            'compare_at_price' => $data['compare_at_price'] ?? null,
            'status' => $data['status'],
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'published_at' => $data['status'] === 'active' && $product->published_at === null
                ? now()
                : $product->published_at,
        ]);

        return back()->with('success', 'Product updated.');
    }

    public function archive(Product $product): RedirectResponse
    {
        $this->authorize('products.manage');

        $product->update(['status' => 'archived']);

        return back()->with('success', 'Product archived.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('products.manage');

        $product->delete();

        return redirect()
            ->route('admin.catalog.products.index')
            ->with('success', 'Product deleted.');
    }

    private function uniqueProductSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 1;

        while (
            Product::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
