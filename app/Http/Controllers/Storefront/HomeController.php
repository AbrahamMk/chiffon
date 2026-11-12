<?php

namespace App\Http\Controllers\Storefront;

use App\Domains\Catalog\Models\Product;
use App\Domains\Marketing\Models\Promotion;
use App\Domains\Marketing\Models\SocialMediaPost;
use App\Http\Controllers\Controller;
use App\Support\Catalog\ProductPresenter;
use App\Support\MediaUploader;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(ProductPresenter $presenter, MediaUploader $media): Response
    {
        $featured = Product::query()
            ->where('status', 'active')
            ->where('is_featured', true)
            ->with(['images', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->limit(8)
            ->get()
            ->map(fn (Product $p) => $presenter->card($p));

        $newArrivals = Product::query()
            ->where('status', 'active')
            ->with(['images', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn (Product $p) => $presenter->card($p));

        $now = now();

        $promotions = Promotion::query()
            ->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Promotion $promo) => [
                'id' => $promo->id,
                'title' => $promo->title,
                'slug' => $promo->slug,
                'description' => $promo->description,
                'link_url' => $promo->link_url,
                'banner_url' => $media->url($promo->banner_path),
            ]);

        $socialPosts = SocialMediaPost::query()
            ->where('is_active', true)
            ->orderByDesc('published_at')
            ->limit(12)
            ->get()
            ->map(fn (SocialMediaPost $post) => [
                'id' => $post->id,
                'platform' => $post->platform,
                'title' => $post->title,
                'caption' => $post->caption,
                'url' => $post->url,
                'media_url' => $media->url($post->media_path),
                'is_featured' => $post->is_featured,
            ]);

        return Inertia::render('Storefront/Home', [
            'featuredProducts' => $featured,
            'newArrivals' => $newArrivals,
            'promotions' => $promotions,
            'socialPosts' => $socialPosts,
        ]);
    }
}
