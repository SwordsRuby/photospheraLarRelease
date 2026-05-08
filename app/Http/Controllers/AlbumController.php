<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\WatermarkService;

class AlbumController extends Controller
{
    protected WatermarkService $watermarkService;

    public function __construct(WatermarkService $watermarkService)
    {
        $this->watermarkService = $watermarkService;
    }

    /**
     * Display user's albums.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $albums = Auth::user()->albums()
            ->withCount('images')
            ->latest()
            ->paginate(12);

        return view('user.albums.index', compact('albums'));
    }

    /**
     * Show form to create a new album.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('user.albums.create');
    }

    /**
     * Store a newly created album.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $album = Album::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('album_covers', 'public');
            $album->update(['cover_img' => '/storage/' . $path]);
        }

        return redirect()->route('user.albums.show', $album->id)
            ->with('success', 'Альбом успешно создан');
    }

    /**
     * Display a single album with its images.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $album = Album::where('user_id', Auth::id())
            ->with([
                'images' => function ($query) {
                    $query->where('is_approved', true);
                }
            ])
            ->findOrFail($id);

        $userImages = Auth::user()->images()
            ->where('is_approved', true)
            ->whereDoesntHave('albums', function ($q) use ($id) {
                $q->where('album_id', $id);
            })
            ->latest()
            ->paginate(12, ['*'], 'available_page');

        return view('user.albums.show', compact('album', 'userImages'));
    }

    /**
     * Show form to edit an album.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $album = Album::where('user_id', Auth::id())->findOrFail($id);
        return view('user.albums.edit', compact('album'));
    }

    /**
     * Update an album.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $album = Album::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $album->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->hasFile('cover')) {
            if ($album->cover_img && $album->cover_img !== 'img/main/photo-image.png') {
                $oldPath = str_replace('/storage/', '', $album->cover_img);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $file = $request->file('cover');
            $filename = 'album_cover_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('album_covers', $filename, 'public');
            $album->update(['cover_img' => '/storage/' . $path]);
        }

        return redirect()->route('user.albums.show', $album->id)
            ->with('success', 'Альбом обновлен');
    }

    /**
     * Add an image to an album.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function addImage(Request $request, $id)
    {
        $album = Album::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);

        $image = Image::where('author_id', Auth::id())
            ->where('id', $request->image_id)
            ->first();

        if (!$image) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Изображение не найдено'], 404);
            }
            return back()->with('error', 'Изображение не найдено');
        }

        if (!$album->images()->where('image_id', $image->id)->exists()) {
            $album->images()->attach($image->id);

            if (!$album->cover_img) {
                $coverPath = $this->copyImageForCover($image->img);
                $album->update(['cover_img' => $coverPath]);
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Изображение добавлено в альбом']);
        }

        return back()->with('success', 'Изображение добавлено в альбом');
    }

    /**
     * Remove an image from an album.
     *
     * @param int $albumId
     * @param int $imageId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeImage($albumId, $imageId)
    {
        $album = Album::where('user_id', Auth::id())->findOrFail($albumId);

        $album->images()->detach($imageId);

        $firstImage = $album->images()->first();
        if ($firstImage) {
            $coverPath = $this->copyImageForCover($firstImage->img);
            $album->update(['cover_img' => $coverPath]);
        } else {
            $album->update(['cover_img' => null]);
        }

        return back()->with('success', 'Изображение удалено из альбома');
    }

    /**
     * Copy an image to use as album cover.
     * This ensures that deleting the original image won't break the cover.
     *
     * @param string $originalPath
     * @return string
     */
    private function copyImageForCover(string $originalPath): string
    {
        $relativePath = str_replace('/storage/', '', $originalPath);

        if (Storage::disk('public')->exists($relativePath)) {
            $content = Storage::disk('public')->get($relativePath);
            $extension = pathinfo($relativePath, PATHINFO_EXTENSION);
            $filename = 'album_cover_' . time() . '_' . Str::random(10) . '.' . $extension;
            $newPath = 'album_covers/' . $filename;

            Storage::disk('public')->put($newPath, $content);

            return '/storage/' . $newPath;
        }

        return $originalPath;
    }

    /**
     * Delete an album.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $album = Album::where('user_id', Auth::id())->findOrFail($id);

        if ($album->cover_img) {
            $coverPath = str_replace('/storage/', '', $album->cover_img);
            if (Storage::disk('public')->exists($coverPath)) {
                Storage::disk('public')->delete($coverPath);
            }
        }

        $album->images()->detach();
        $album->delete();

        return redirect()->route('user.albums.index')
            ->with('success', 'Альбом удален');
    }

    /**
     * Generate a share link for the album.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateShareLink(int $id)
    {
        $album = Album::where('user_id', Auth::id())->findOrFail($id);

        if ($album->images()->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя создать ссылку для пустого альбома. Добавьте хотя бы одно изображение.'
            ], 400);
        }

        $shareUrl = $album->enableSharing(30); // 30 days expiration

        return response()->json([
            'success' => true,
            'share_url' => $album->share_url,
            'share_token' => $album->share_token,
            'message' => 'Публичная ссылка создана'
        ]);
    }

    /**
     * Disable share link for the album.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function disableShareLink(int $id)
    {
        $album = Album::where('user_id', Auth::id())->findOrFail($id);
        $album->disableSharing();

        return response()->json([
            'success' => true,
            'message' => 'Публичная ссылка отключена'
        ]);
    }

    /**
     * Regenerate share link for the album.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function regenerateShareLink(int $id)
    {
        $album = Album::where('user_id', Auth::id())->findOrFail($id);
        $album->disableSharing();
        $shareUrl = $album->enableSharing(30);

        return response()->json([
            'success' => true,
            'share_url' => $album->share_url,
            'share_token' => $album->share_token,
            'message' => 'Публичная ссылка обновлена'
        ]);
    }

    /**
     * Display shared album via public link.
     *
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function showShared(string $token)
    {
        $album = Album::findByShareToken($token);

        if (!$album) {
            abort(404, 'Альбом не найден или ссылка недействительна');
        }

        $images = $album->images()
            ->where('is_approved', true)
            ->latest()
            ->get();

        // Apply watermark for guests
        if (!Auth::check()) {
            $images->each(function ($image) {
                $image->display_img = $this->watermarkService->getWatermarkedImage($image->img);
            });
        }

        return view('user.albums.shared', compact('album', 'images'));
    }

    /**
     * Display single image from shared album on a dedicated page.
     *
     * @param string $token
     * @param int $imageId
     * @return \Illuminate\View\View
     */
    public function showSharedImage(string $token, int $imageId)
    {
        $album = Album::findByShareToken($token);

        if (!$album) {
            abort(404, 'Альбом не найден или ссылка недействительна');
        }

        // Get the image - must belong to album and be approved
        $image = Image::where('id', $imageId)
            ->where('is_approved', true)
            ->whereHas('albums', function ($query) use ($album) {
                $query->where('album_id', $album->id);
            })
            ->with(['category', 'tags', 'author'])
            ->first();

        if (!$image) {
            abort(404, 'Изображение не найдено в этом альбоме');
        }

        $displayImage = null;
        if (!Auth::check()) {
            $displayImage = $this->watermarkService->getWatermarkedImage($image->img);
        }

        $imageIds = $album->images()
            ->where('images.is_approved', true)
            ->orderBy('images.created_at', 'desc')
            ->pluck('images.id')
            ->toArray();

        $currentIndex = array_search($imageId, $imageIds);

        $prevImage = null;
        $nextImage = null;

        if ($currentIndex !== false) {
            if ($currentIndex > 0) {
                $prevImage = Image::where('id', $imageIds[$currentIndex - 1])
                    ->with(['category', 'tags'])
                    ->first();
                if (!Auth::check() && $prevImage) {
                    $prevImage->img = $this->watermarkService->getWatermarkedImage($prevImage->img);
                }
            }
            if ($currentIndex < count($imageIds) - 1) {
                $nextImage = Image::where('id', $imageIds[$currentIndex + 1])
                    ->with(['category', 'tags'])
                    ->first();
                if (!Auth::check() && $nextImage) {
                    $nextImage->img = $this->watermarkService->getWatermarkedImage($nextImage->img);
                }
            }
        }

        $totalImages = count($imageIds);
        $currentPosition = $currentIndex !== false ? $currentIndex + 1 : 1;

        return view('user.albums.shared-image', compact(
            'album',
            'image',
            'displayImage',
            'prevImage',
            'nextImage',
            'totalImages',
            'currentPosition'
        ));
    }
}