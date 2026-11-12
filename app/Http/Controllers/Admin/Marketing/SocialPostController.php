<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Domains\Marketing\Models\SocialMediaPost;
use App\Http\Controllers\Controller;
use App\Support\MediaUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SocialPostController extends Controller
{
    public function index(): Response
    {
        $this->authorize('marketing.manage');

        return Inertia::render('Admin/Marketing/SocialPosts/Index', [
            'posts' => SocialMediaPost::query()->latest()->paginate(20),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('marketing.manage');

        return Inertia::render('Admin/Marketing/SocialPosts/Form', [
            'post' => null,
        ]);
    }

    public function store(Request $request, MediaUploader $media): RedirectResponse
    {
        $this->authorize('marketing.manage');

        SocialMediaPost::query()->create($this->validated($request, $media));

        return redirect()->route('admin.marketing.social-posts.index')->with('success', 'Post created.');
    }

    public function edit(SocialMediaPost $socialPost): Response
    {
        $this->authorize('marketing.manage');

        return Inertia::render('Admin/Marketing/SocialPosts/Form', [
            'post' => $socialPost,
        ]);
    }

    public function update(Request $request, SocialMediaPost $socialPost, MediaUploader $media): RedirectResponse
    {
        $this->authorize('marketing.manage');

        $socialPost->update($this->validated($request, $media, $socialPost));

        return redirect()->route('admin.marketing.social-posts.index')->with('success', 'Post updated.');
    }

    public function destroy(SocialMediaPost $socialPost, MediaUploader $media): RedirectResponse
    {
        $this->authorize('marketing.manage');

        $media->delete($socialPost->media_path);
        $socialPost->delete();

        return redirect()->route('admin.marketing.social-posts.index')->with('success', 'Post deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, MediaUploader $media, ?SocialMediaPost $existing = null): array
    {
        $data = $request->validate([
            'platform' => ['required', 'string', 'max:50'],
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:5000'],
            'url' => ['nullable', 'url', 'max:500'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'media' => ['nullable', 'file', 'max:10240'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('media')) {
            if ($existing?->media_path) {
                $media->delete($existing->media_path);
            }
            $data['media_path'] = $media->image($request->file('media'), 'social');
            $data['media_type'] = str_starts_with($request->file('media')->getMimeType() ?? '', 'video/') ? 'video' : 'image';
        }

        unset($data['media']);

        return $data;
    }
}
