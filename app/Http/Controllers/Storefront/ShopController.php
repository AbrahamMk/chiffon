<?php

namespace App\Http\Controllers\Storefront;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Support\Catalog\ProductPresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(Request $request, ProductPresenter $presenter): Response
    {
        $query = Product::query()
            ->where('status', 'active')
            ->with(['images', 'category', 'variants' => fn ($q) => $q->where('is_active', true)]);

        if ($category = $request->string('category')->toString()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $category));
        }

        if ($search = trim($request->string('q')->toString())) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $sort = $request->string('sort', 'newest')->toString();
        match ($sort) {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('published_at')->orderByDesc('created_at'),
        };

        $products = $query->paginate(12)->withQueryString();
        $products->through(fn (Product $product) => $presenter->card($product));

        return Inertia::render('Storefront/Shop/Index', [
            'products' => $products,
            'categories' => ProductCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug']),
            'filters' => [
                'q' => $request->input('q'),
                'category' => $request->input('category'),
                'sort' => $sort,
            ],
        ]);
    }

    public function show(string $slug, ProductPresenter $presenter): Response
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $related = Product::query()
            ->where('status', 'active')
            ->where('id', '!=', $product->id)
            ->when(
                $product->product_category_id,
                fn ($q) => $q->where('product_category_id', $product->product_category_id)
            )
            ->with(['images', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->limit(4)
            ->get()
            ->map(fn (Product $p) => $presenter->card($p));

        return Inertia::render('Storefront/Shop/Show', [
            'product' => $presenter->detail($product),
            'related' => $related,
        ]);
    }
}
