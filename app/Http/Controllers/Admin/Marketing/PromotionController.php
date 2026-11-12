<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Domains\Marketing\Models\Promotion;
use App\Http\Controllers\Controller;
use App\Support\MediaUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PromotionController extends Controller
{
    public function index(): Response
    {
        $this->authorize('marketing.manage');

        return Inertia::render('Admin/Marketing/Promotions/Index', [
            'promotions' => Promotion::query()->latest()->paginate(20),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('marketing.manage');

        return Inertia::render('Admin/Marketing/Promotions/Form', [
            'promotion' => null,
        ]);
    }

    public function store(Request $request, MediaUploader $media): RedirectResponse
    {
        $this->authorize('marketing.manage');

        $validated = $this->validated($request, $media);

        Promotion::query()->create($validated);

        return redirect()->route('admin.marketing.promotions.index')->with('success', 'Promotion created.');
    }

    public function edit(Promotion $promotion): Response
    {
        $this->authorize('marketing.manage');

        return Inertia::render('Admin/Marketing/Promotions/Form', [
            'promotion' => $promotion,
        ]);
    }

    public function update(Request $request, Promotion $promotion, MediaUploader $media): RedirectResponse
    {
        $this->authorize('marketing.manage');

        $promotion->update($this->validated($request, $media, $promotion));

        return redirect()->route('admin.marketing.promotions.index')->with('success', 'Promotion updated.');
    }

    public function destroy(Promotion $promotion, MediaUploader $media): RedirectResponse
    {
        $this->authorize('marketing.manage');

        $media->delete($promotion->banner_path);
        $promotion->delete();

        return redirect()->route('admin.marketing.promotions.index')->with('success', 'Promotion deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, MediaUploader $media, ?Promotion $existing = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'link_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'banner' => ['nullable', 'image', 'max:5120'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        if ($request->hasFile('banner')) {
            if ($existing?->banner_path) {
                $media->delete($existing->banner_path);
            }
            $data['banner_path'] = $media->image($request->file('banner'), 'promotions');
        }

        unset($data['banner']);

        return $data;
    }
}
