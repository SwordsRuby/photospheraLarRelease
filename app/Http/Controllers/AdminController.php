<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Image;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Delete image file from storage.
     *
     * @param string|null $path
     * @return void
     */
    private function deleteImageFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists(str_replace('/storage/', '', $path))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $path));
        }
    }

    /**
     * Handle image upload or URL download.
     *
     * @param Request $request
     * @param string $directory
     * @return string|null
     */
    private function handleImageUpload(Request $request, string $directory): ?string
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $path = $request->file('image')->store("{$directory}", 'public');
            return "/storage/{$path}";
        }

        if ($request->filled('image_url')) {
            return $this->downloadImage($request->image_url, $directory);
        }

        return null;
    }

    /**
     * Download image from URL.
     *
     * @param string $url
     * @param string $directory
     * @return string|null
     */
    private function downloadImage(string $url, string $directory): ?string
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
            $info = curl_getinfo($ch);
            curl_close($ch);

            if ($info['http_code'] !== 200 || empty($data)) {
                return null;
            }

            $extension = $this->getImageExtension($info['content_type'] ?? '', $url);
            $filename = "{$directory}_" . time() . '_' . Str::random(10) . '.' . $extension;

            Storage::disk('public')->put("{$directory}/{$filename}", $data);

            return "/storage/{$directory}/{$filename}";
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get image extension from content type or URL.
     *
     * @param string $contentType
     * @param string $url
     * @return string
     */
    private function getImageExtension(string $contentType, string $url): string
    {
        $extensions = [
            'jpeg' => 'jpg',
            'jpg' => 'jpg',
            'png' => 'png',
            'webp' => 'webp',
            'svg' => 'svg',
            'gif' => 'gif',
        ];

        foreach ($extensions as $mime => $ext) {
            if (str_contains($contentType, $mime)) {
                return $ext;
            }
        }

        $path = parse_url($url, PHP_URL_PATH);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, array_keys($extensions)) ? $ext : 'jpg';
    }

    //  IMAGES MANAGEMENT 

    /**
     * Display unapproved images for moderation.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function images(Request $request)
    {
        $images = Image::with(['author', 'category', 'tags'])
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('is_approved', 'asc')
            ->latest()
            ->get();

        return view('admin.images', compact('images'));
    }

    /**
     * Approve an image.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveImage(int $id)
    {
        $image = Image::findOrFail($id);
        $image->update(['is_approved' => true]);

        // return back()->with('success', 'Изображение одобрено');
        return back();
    }

    /**
     * Delete an image.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyImage(int $id)
    {
        $image = Image::findOrFail($id);

        $this->deleteImageFile($image->img);

        $image->likes()->detach();
        $image->favorites()->detach();
        $image->tags()->detach();
        $image->delete();

        // return back()->with('success', 'Изображение удалено');
        return back();
    }

    //  USERS MANAGEMENT 

    /**
     * Display all non-moderator users.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function users(Request $request)
    {
        $users = User::where('is_moderator', false)
            ->when($request->filled('search'), fn($q) => $q->where('login', 'like', "%{$request->search}%"))
            ->latest()
            ->get();

        return view('admin.users', compact('users'));
    }

    /**
     * Ban a user.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function banUser(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Нельзя забанить самого себя');
        }

        $user->update(['is_banned' => true]);

        return back()->with('success', 'Пользователь забанен');
    }

    /**
     * Unban a user.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unbanUser(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_banned' => false]);

        return back()->with('success', 'Бан снят');
    }

    //  MODERATORS MANAGEMENT 

    /**
     * Display all moderators.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function moderators(Request $request)
    {
        $moderators = User::where('is_moderator', true)
            ->when($request->filled('search'), fn($q) => $q->where('login', 'like', "%{$request->search}%"))
            ->latest()
            ->get();

        return view('admin.moderators', compact('moderators'));
    }

    /**
     * Create a new moderator with email verification.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createModerator(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string|max:50|unique:users,login',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = User::create([
                'login' => $validated['login'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'img' => '/img/users/default-user-icon.png',
                'is_moderator' => true,
                'is_banned' => false,
            ]);

            // Generate and send verification code
            $code = $user->generateVerificationCode();
            Mail::to($user->email)->send(new VerificationCodeMail($code, $user->login));

            return redirect()->route('admin.moderators')
                ->with('success', 'Модератор создан. Код подтверждения отправлен на почту: ' . $user->email)
                ->with('showVerificationModal', true)
                ->with('verificationUserId', $user->id);

        } catch (\Exception $e) {
            Log::error('Moderator creation failed: ' . $e->getMessage());

            return redirect()->route('admin.moderators')
                ->with('error', 'Ошибка при создании модератора: ' . $e->getMessage());
        }
    }

    /**
     * Resend verification code for moderator.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendModeratorCode(int $id)
    {
        $user = User::findOrFail($id);

        if (!$user->is_moderator) {
            return redirect()->route('admin.moderators')
                ->with('error', 'Пользователь не является модератором');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('admin.moderators')
                ->with('info', 'Почта уже подтверждена');
        }

        try {
            $code = $user->generateVerificationCode();
            Mail::to($user->email)->send(new VerificationCodeMail($code, $user->login));

            return redirect()->route('admin.moderators')
                ->with('success', 'Новый код подтверждения отправлен на почту: ' . $user->email);
        } catch (\Exception $e) {
            return redirect()->route('admin.moderators')
                ->with('error', 'Не удалось отправить код: ' . $e->getMessage());
        }
    }

    /**
     * Verify moderator email.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyModerator(Request $request, int $id)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        $user = User::findOrFail($id);

        if (!$user->is_moderator) {
            return redirect()->route('admin.moderators')
                ->with('error', 'Пользователь не является модератором');
        }

        if ($user->verifyCode($request->verification_code)) {
            return redirect()->route('admin.moderators')
                ->with('success', 'Почта модератора "' . $user->login . '" успешно подтверждена!');
        }

        return redirect()->route('admin.moderators')
            ->with('error', 'Неверный или просроченный код подтверждения');
    }

    /**
     * Delete a moderator.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyModerator(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Нельзя удалить самого себя');
        }

        $user->delete();

        return back()->with('success', 'Модератор удален');
    }

    //  CATEGORIES MANAGEMENT 

    /**
     * Display all categories.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function categories(Request $request)
    {
        $categories = Category::query()
            ->where('id', '!=', 1)
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->get();

        return view('admin.categories', compact('categories'));
    }

    /**
     * Create a new category.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'image_url' => 'nullable|url',
        ]);

        $imagePath = $this->handleImageUpload($request, 'categories');

        if (!$imagePath) {
            return back()->with('error', 'Необходимо загрузить изображение категории')->withInput();
        }

        Category::create([
            'name' => $validated['name'],
            'img' => $imagePath,
        ]);

        return back()->with('success', 'Категория успешно создана');
    }

    /**
     * Update a category.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCategory(Request $request, int $id)
    {
        if ($id != 1) {
            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:categories,name,' . $id,
            ]);

            $category->update(['name' => $validated['name']]);

            return redirect()->route('admin.categories')->with('success', 'Категория обновлена');
        } else {
            return back()->with('error', 'Данную категорию отредактировать нельзя');
        }
    }

    /**
     * Delete a category.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyCategory(int $id)
    {
        if ($id != 1) {
            $category = Category::findOrFail($id);

            $this->deleteImageFile($category->img);
            $category->delete();

            return back()->with('success', 'Категория удалена');
        } else {
            return back()->with('error', 'Данную категорию удалить нельзя');
        }
    }

    //  TAGS MANAGEMENT 

    /**
     * Display all tags.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function tags(Request $request)
    {
        $tags = Tag::query()
            ->when($request->filled('search'), fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->latest()
            ->get();

        return view('admin.tags', compact('tags'));
    }

    /**
     * Create a new tag.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createTag(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100|unique:tags,title',
        ]);

        Tag::create(['title' => $validated['title']]);

        return back()->with('success', 'Тег создан');
    }

    /**
     * Update a tag.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTag(Request $request, int $id)
    {
        $tag = Tag::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:100|unique:tags,title,' . $id,
        ]);

        $tag->update(['title' => $validated['title']]);

        return redirect()->route('admin.tags')->with('success', 'Тег обновлен');
    }

    /**
     * Delete a tag.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyTag(int $id)
    {
        $tag = Tag::findOrFail($id);

        $tag->images()->detach();
        $tag->delete();

        return back()->with('success', 'Тег удален');
    }
}