<?php

namespace App\Http\Controllers;

use App\Http\Requests\Image\StoreImageRequest;
use App\Http\Requests\Image\UpdateImageRequest;
use App\Models\Category;
use App\Models\Image;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Services\WatermarkService;


class ImageController extends Controller
{
    /**
     * Display a listing of approved public images.
     *
     * @param Request $request
     * @param WatermarkService $watermarkService
     * @return \Illuminate\View\View
     */
    public function index(Request $request, WatermarkService $watermarkService)
    {
        $images = Image::with(['author', 'category', 'tags'])
            ->where('is_approved', true)
            ->where('is_private', false)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('tags', fn($tagQ) => $tagQ->where('title', 'like', "%{$search}%"));
                });
            })
            ->when(
                $request->filled('category') && $request->category != '0',
                fn($q) => $q->where('category_id', $request->category)
            )
            ->latest()->get();

        $categories = Category::orderByDesc('name')->where('id', '!=', 1)->get();

        $images->each(function ($image) use ($watermarkService) {
            $image->display_img = $watermarkService->getWatermarkedImage($image->img);
        });

        return view('images.index', compact('images', 'categories'));
    }

    /**
     * Display a single approved image.
     *
     * @param int $id
     * @param WatermarkService $watermarkService
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(int $id, WatermarkService $watermarkService)
    {
        $image = Image::with(['author', 'category', 'tags'])
            ->where('is_approved', true)
            ->findOrFail($id);

        if ($image->is_private) {
            if (auth()->check() && $image->author_id === auth()->id()) {
                return redirect()->route('images.edit', $image->id);
            }
            abort(403, 'Это изображение находится в приватном альбоме автора');
        }

        $displayImage = $watermarkService->getWatermarkedImage($image->img);

        return view('images.show', compact('image', 'displayImage'));
    }

    /**
     * Show the form for creating a new image.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $albums = Auth::check() ? Auth::user()->albums()->withCount('images')->get() : collect();

        return view('images.create', [
            'categories' => Category::orderByDesc('name')->where('id', '!=', 1)->get(),
            'tags' => Tag::orderByDesc('title')->get(),
            'albums' => $albums,
        ]);
    }

    /**
     * Store a newly created image.
     *
     * @param StoreImageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreImageRequest $request)
    {
        $subscription = Auth::user()->active_subscription;
        $fileSize = $this->getImageFileSize($request);

        if (!$this->hasEnoughSpace($subscription, $fileSize)) {
            return back()->with('error', 'Недостаточно места в хранилище. Оформите подписку для увеличения объема.')
                ->withInput();
        }

        $imagePath = $this->storeImageFromRequest($request);
        $actualFileSize = $this->getActualFileSize($imagePath);

        $image = Image::create([
            'img' => $imagePath,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'author_id' => $request->user()->id,
            'is_approved' => $request->boolean('is_private', false),
            'is_private' => $request->boolean('is_private', false),
        ]);

        if ($request->filled('tags')) {
            $image->tags()->sync($request->tags);
        }

        if ($request->filled('album_id')) {
            $album = Auth::user()->albums()->find($request->album_id);
            if ($album) {
                if (!$album->images()->where('image_id', $image->id)->exists()) {
                    $album->images()->attach($image->id);

                    if (!$album->cover_img) {
                        $coverPath = $this->copyImageForCover($imagePath);
                        $album->update(['cover_img' => $coverPath]);
                    }
                }
            }
        }

        $subscription->update([
            'storage_used' => $subscription->storage_used + $actualFileSize
        ]);

        return to_route('images.index')->with('success', $request->boolean('is_private', false) 
        ? 'Изображение успешно добавлено' : 'Изображение успешно добавлено и ожидает модерации');
    }

    /**
     * Copy image for album cover.
     *
     * @param string $imagePath
     * @return string
     */
    private function copyImageForCover(string $imagePath): string
    {
        $relativePath = str_replace('/storage/', '', $imagePath);

        if (Storage::disk('public')->exists($relativePath)) {
            $content = Storage::disk('public')->get($relativePath);
            $extension = pathinfo($relativePath, PATHINFO_EXTENSION);
            $filename = 'album_cover_' . time() . '_' . Str::random(10) . '.' . $extension;
            $newPath = 'album_covers/' . $filename;

            Storage::disk('public')->put($newPath, $content);

            return '/storage/' . $newPath;
        }

        return $imagePath;
    }

    /**
     * Show the form for editing the specified image.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $image = Image::where('author_id', request()->user()->id)
            ->findOrFail($id);

        $albums = Auth::user()->albums()->withCount('images')->get();

        $imageAlbumIds = $image->albums()->pluck('albums.id')->toArray();

        return view('images.edit', [
            'image' => $image,
            'categories' => Category::orderByDesc('name')->where('id', '!=', 1)->get(),
            'tags' => Tag::orderByDesc('title')->get(),
            'albums' => $albums,
            'imageAlbumIds' => $imageAlbumIds,
        ]);
    }

    /**
     * Update the specified image.
     *
     * @param UpdateImageRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateImageRequest $request, int $id)
    {
        $image = Image::where('author_id', $request->user()->id)
            ->findOrFail($id);

        $image->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'is_approved' => $request->boolean('is_private', false),
            'is_private' => $request->boolean('is_private', false),
        ]);

        if ($request->hasFile('image') || $request->filled('image_url')) {
            $this->updateStorageUsage($image->img, true);

            $this->deleteImageFile($image->img);
            $newPath = $this->storeImageFromRequest($request);
            $image->update(['img' => $newPath]);

            $this->updateStorageUsage($newPath, false);
        }

        $image->tags()->sync($request->tags ?? []);

        if ($request->filled('album_id')) {
            $album = Auth::user()->albums()->find($request->album_id);
            if ($album && !$album->images()->where('image_id', $image->id)->exists()) {
                $album->images()->attach($image->id);

                if (!$album->cover_img) {
                    $coverPath = $this->copyImageForCover($image->img);
                    $album->update(['cover_img' => $coverPath]);
                }
            }
        }

        return to_route('user.added')->with('success', $request->boolean('is_private', false) ? 'Изображение обновлено' : 'Изображение обновлено и отправлено на модерацию');
    }

    /**
     * Delete the specified image.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, int $id)
    {
        $image = Image::findOrFail($id);

        if (!$request->user()->is_moderator && $image->author_id !== $request->user()->id) {
            abort(403);
        }

        $this->updateStorageUsage($image->img, true);

        $this->deleteImageFile($image->img);

        $image->likes()->detach();
        $image->favorites()->detach();
        $image->tags()->detach();
        $image->albums()->detach();
        $image->delete();

        return to_route('user.added')->with('success', 'Изображение удалено');
    }

    /**
     * Toggle like on an image.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function like(Request $request, int $id)
    {
        $image = Image::findOrFail($id);
        $userId = $request->user()->id;

        $liked = !$image->likes()->where('user_id', $userId)->exists();

        if ($liked) {
            $image->likes()->attach($userId);
        } else {
            $image->likes()->detach($userId);
        }

        return $request->ajax()
            ? response()->json(['liked' => $liked, 'count' => $image->likes()->count()])
            : back();
    }

    /**
     * Toggle favorite on an image.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function favorite(Request $request, int $id)
    {
        $image = Image::findOrFail($id);
        $userId = $request->user()->id;

        $favorited = !$image->favorites()->where('user_id', $userId)->exists();

        if ($favorited) {
            $image->favorites()->attach($userId);
        } else {
            $image->favorites()->detach($userId);
        }

        return $request->ajax()
            ? response()->json(['favorited' => $favorited])
            : back();
    }

    /**
     * Display user's private images.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function privateImages(Request $request)
    {
        $images = Auth::user()->images()
            ->with(['category', 'tags', 'albums'])
            ->where('is_approved', true)
            ->where('is_private', true)
            ->latest()
            ->paginate(20);

        return view('user.private', compact('images'));
    }

    /**
     * Store image from file upload or URL.
     *
     * @param Request $request
     * @return string
     * @throws \RuntimeException
     */
    private function storeImageFromRequest(Request $request): string
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $path = $request->file('image')->store('images', 'public');
            return '/storage/' . $path;
        }

        if ($request->filled('image_url')) {
            return $this->downloadImageFromUrl($request->image_url);
        }

        throw new \RuntimeException('Необходимо загрузить изображение или указать URL');
    }

    /**
     * Download image from external URL.
     *
     * @param string $url
     * @param int $maxSize
     * @return string
     * @throws \RuntimeException
     */
    private function downloadImageFromUrl(string $url, int $maxSize = 5242880): string
    {
        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_USERAGENT => 'Mozilla/5.0',
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $data = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            curl_close($ch);

            if ($httpCode !== 200 || empty($data) || $contentLength > $maxSize) {
                throw new \RuntimeException('Download failed');
            }

            $extension = $this->detectExtension($url, $contentType);
            $filename = 'image_' . Str::random(20) . '_' . time() . '.' . $extension;

            Storage::disk('public')->put('images/' . $filename, $data);

            return '/storage/images/' . $filename;
        } catch (\Exception $e) {
            Log::error('Image download failed: ' . $e->getMessage());
            throw new \RuntimeException('Не удалось загрузить изображение по указанному URL');
        }
    }

    /**
     * Detect image extension from content type or URL.
     *
     * @param string $url
     * @param string $contentType
     * @return string
     */
    private function detectExtension(string $url, string $contentType): string
    {
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
        ];

        foreach ($mimeMap as $mime => $ext) {
            if (str_contains($contentType, $mime)) {
                return $ext;
            }
        }

        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']) ? $extension : 'jpg';
    }

    /**
     * Delete image file from storage.
     *
     * @param string|null $path
     * @return void
     */
    private function deleteImageFile(?string $path): void
    {
        if ($path) {
            $relativePath = str_replace('/storage/', '', $path);
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        }
    }

    /**
     * Get file size from request.
     *
     * @param Request $request
     * @return int
     */
    private function getImageFileSize(Request $request): int
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            return $request->file('image')->getSize();
        }

        if ($request->filled('image_url')) {
            return 0;
        }

        return 0;
    }

    /**
     * Get actual file size from storage.
     *
     * @param string $path
     * @return int
     */
    private function getActualFileSize(string $path): int
    {
        $relativePath = str_replace('/storage/', '', $path);
        if (Storage::disk('public')->exists($relativePath)) {
            return Storage::disk('public')->size($relativePath);
        }
        return 0;
    }

    /**
     * Check if user has enough storage space.
     *
     * @param mixed $subscription
     * @param int $fileSize
     * @return bool
     */
    private function hasEnoughSpace($subscription, int $fileSize): bool
    {
        $remainingSpace = $subscription->storage_limit - $subscription->storage_used;

        $requiredSpace = $fileSize > 0 ? $fileSize * 1.1 : 1048576; // 1MB minimum buffer

        return $remainingSpace >= $requiredSpace;
    }

    /**
     * Update storage usage for user subscription.
     *
     * @param string $imagePath
     * @param bool $isRemoving
     * @return void
     */
    private function updateStorageUsage(string $imagePath, bool $isRemoving): void
    {
        $subscription = Auth::user()->active_subscription;
        $fileSize = $this->getActualFileSize($imagePath);

        if ($fileSize > 0) {
            if ($isRemoving) {
                $subscription->update([
                    'storage_used' => max(0, $subscription->storage_used - $fileSize)
                ]);
            } else {
                $subscription->update([
                    'storage_used' => $subscription->storage_used + $fileSize
                ]);
            }
        }
    }
}