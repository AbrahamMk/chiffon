<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Domains\Catalog\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\StoreProductCategoryRequest;
use App\Http\Requests\Admin\Catalog\UpdateProductCategoryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('products.manage');

        $categories = ProductCategory::query()
            ->with('parent:id,name')
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Catalog/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('products.manage');

        return Inertia::render('Admin/Catalog/Categories/Form', [
            'category' => null,
            'parents' => $this->parentOptions(),
        ]);
    }

    public function store(StoreProductCategoryRequest $request): RedirectResponse
    {
        $this->authorize('products.manage');

        $data = $request->validated();

        ProductCategory::query()->create([
            ...$data,
            'slug' => $this->uniqueCategorySlug($data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()
            ->route('admin.catalog.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(ProductCategory $category): Response
    {
        $this->authorize('products.manage');

        return Inertia::render('Admin/Catalog/Categories/Form', [
            'category' => $category,
            'parents' => $this->parentOptions($category->id),
        ]);
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $category): RedirectResponse
    {
        $this->authorize('products.manage');

        $data = $request->validated();

        $category->update([
            ...$data,
            'slug' => $this->uniqueCategorySlug($data['name'], $category->id),
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
        ]);

        return redirect()
            ->route('admin.catalog.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(ProductCategory $category): RedirectResponse
    {
        $this->authorize('products.manage');

        $category->delete();

        return redirect()
            ->route('admin.catalog.categories.index')
            ->with('success', 'Category deleted.');
    }

    /**
     * @return list<array{id: int, name: string}>
     */
    private function parentOptions(?int $exceptId = null): array
    {
        return ProductCategory::query()
            ->when($exceptId, fn ($q) => $q->whereKeyNot($exceptId))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->all();
    }

    private function uniqueCategorySlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 1;

        while (
            ProductCategory::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
