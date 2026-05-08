<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display user's favorite images.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function favorites(Request $request)
    {
        $images = $request->user()->favorites()
            ->with(['author', 'category', 'tags'])
            ->where('is_approved', true)
            ->latest()
            ->paginate(12);

        return view('user.favorites', compact('images'));
    }

    /**
     * Display user's uploaded images.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function added(Request $request)
    {
        $images = $request->user()->images()
            ->with(['category', 'tags'])
            ->where('is_private', false)
            ->withCount('likes')
            ->latest()
            ->paginate(12);

        return view('user.added', compact('images'));
    }

    /**
     * Display user's private images.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function private(Request $request)
    {
        $images = $request->user()->images()
            ->with(['category', 'tags', 'albums'])
            ->where('is_private', true)
            ->latest()
            ->paginate(12);

        return view('user.private', compact('images'));
    }

    /**
     * Display storage usage statistics.
     *
     * @return \Illuminate\View\View
     */
    public function storage()
    {
        $subscription = Auth::user()->active_subscription;
        $images = Auth::user()->images()
            ->where('is_approved', true)
            ->get();

        $totalSize = 0;
        foreach ($images as $image) {
            $path = str_replace('/storage/', '', $image->img);
            if (Storage::disk('public')->exists($path)) {
                $totalSize += Storage::disk('public')->size($path);
            }
        }

        $subscription->update(['storage_used' => $totalSize]);

        $storageData = [
            'total' => $totalSize,
            'limit' => $subscription->storage_limit,
            'used_percent' => $subscription->storage_used_percentage,
            'remaining' => $subscription->remaining_storage,
            'remaining_formatted' => $this->formatBytes($subscription->remaining_storage),
            'total_formatted' => $this->formatBytes($totalSize),
            'limit_formatted' => $this->formatBytes($subscription->storage_limit),
        ];

        return view('user.storage', compact('storageData', 'subscription'));
    }

    /**
     * Update user profile information.
     *
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $user->login = $request->login;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->hasFile('image')) {
            $this->deleteOldUserImage($user->img);
            $user->img = $this->storeUserImage($request->file('image'));
        }

        $user->save();

        return back()->with('success', 'Профиль успешно обновлен');
    }

    /**
     * Delete user account and all associated data.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        foreach ($user->images as $image) {
            $this->deleteImageFile($image->img);
            $image->likes()->detach();
            $image->favorites()->detach();
            $image->tags()->detach();
            $image->albums()->detach();
            $image->delete();
        }

        foreach ($user->albums as $album) {
            $album->images()->detach();
            $this->deleteImageFile($album->cover_img);
            $album->delete();
        }

        $user->subscription()->delete();

        $this->deleteOldUserImage($user->img);

        $user->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Аккаунт успешно удален');
    }

    /**
     * Format bytes to human readable format.
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['Б', 'КБ', 'МБ', 'ГБ', 'ТБ'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Delete old user avatar image.
     *
     * @param string $currentImage
     * @return void
     */
    private function deleteOldUserImage(string $currentImage): void
    {
        if ($currentImage !== '/img/users/default-user-icon.png') {
            $path = str_replace('/storage', 'public', $currentImage);
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }
    }

    /**
     * Store uploaded user avatar.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    private function storeUserImage($file): string
    {
        $filename = 'user_' . Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();

        $storedPath = $file->storeAs('user', $filename, 'public');

        return '/storage/' . $storedPath;
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
}