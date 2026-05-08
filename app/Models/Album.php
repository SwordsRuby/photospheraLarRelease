<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Album extends Model
{
    use HasFactory;

    protected $table = 'albums';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'cover_img',
        'share_token',
        'is_shared',
        'share_expires_at',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_shared' => 'boolean',
        'share_expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the album.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the images associated with the album.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function images()
    {
        return $this->belongsToMany(Image::class, 'album_image', 'album_id', 'image_id')
            ->withTimestamps();
    }

    /**
     * Get the cover image URL for the album.
     *
     * @return string
     */
    public function getCoverImageAttribute()
    {
        // If there's a custom cover, use it
        if ($this->cover_img && Storage::disk('public')->exists(str_replace('/storage/', '', $this->cover_img))) {
            return $this->cover_img;
        }

        // If no custom cover, take the first image and copy it
        $firstImage = $this->images()->first();
        if ($firstImage) {
            $this->copyAndSetCover($firstImage->img);
            return $this->cover_img;
        }

        return 'img/main/photo-image.png';
    }

    /**
     * Copy an image and set it as the album cover.
     *
     * @param string $originalPath
     * @return void
     */
    public function copyAndSetCover(string $originalPath): void
    {
        $relativePath = str_replace('/storage/', '', $originalPath);

        if (Storage::disk('public')->exists($relativePath)) {
            $content = Storage::disk('public')->get($relativePath);
            $extension = pathinfo($relativePath, PATHINFO_EXTENSION);
            $filename = 'album_cover_' . time() . '_' . uniqid() . '.' . $extension;
            $newPath = 'album_covers/' . $filename;

            Storage::disk('public')->put($newPath, $content);
            $this->cover_img = '/storage/' . $newPath;
            $this->saveQuietly();
        }
    }

    /**
     * Get the count of images in the album.
     *
     * @return int
     */
    public function getImagesCountAttribute()
    {
        return $this->images()->count();
    }

    /**
     * Generate a unique share token.
     */
    public function generateShareToken(): string
    {
        return Str::random(32);
    }

    /**
     * Enable sharing for the album.
     * 
     * @param int|null $expiresInDays - Days until the share link expires (null = never)
     * @return string - The share token
     */
    public function enableSharing(?int $expiresInDays = null): string
    {
        $this->share_token = $this->generateShareToken();
        $this->is_shared = true;
        $this->share_expires_at = $expiresInDays ? now()->addDays($expiresInDays) : null;
        $this->save();

        return $this->share_token;
    }

    /**
     * Disable sharing for the album.
     */
    public function disableSharing(): void
    {
        $this->share_token = null;
        $this->is_shared = false;
        $this->share_expires_at = null;
        $this->save();
    }

    /**
     * Check if the share link is valid.
     */
    public function isShareValid(): bool
    {
        if (!$this->is_shared || !$this->share_token) {
            return false;
        }

        if ($this->share_expires_at && now()->greaterThan($this->share_expires_at)) {
            return false;
        }

        return true;
    }

    /**
     * Get the share URL.
     */
    public function getShareUrlAttribute(): ?string
    {
        if ($this->isShareValid()) {
            return route('albums.shared', $this->share_token);
        }

        return null;
    }

    /**
     * Find album by share token.
     */
    public static function findByShareToken(string $token): ?self
    {
        $album = self::where('share_token', $token)->first();

        if ($album && $album->isShareValid()) {
            return $album;
        }

        return null;
    }

    /**
     * Get next image in album.
     *
     * @param int $currentImageId
     * @return \App\Models\Image|null
     */
    public function getNextImage(int $currentImageId)
    {
        $imageIds = $this->images()->where('is_approved', true)->pluck('id')->toArray();
        $currentIndex = array_search($currentImageId, $imageIds);

        if ($currentIndex !== false && isset($imageIds[$currentIndex + 1])) {
            return Image::find($imageIds[$currentIndex + 1]);
        }

        return null;
    }

    /**
     * Get previous image in album.
     *
     * @param int $currentImageId
     * @return \App\Models\Image|null
     */
    public function getPrevImage(int $currentImageId)
    {
        $imageIds = $this->images()->where('is_approved', true)->pluck('id')->toArray();
        $currentIndex = array_search($currentImageId, $imageIds);

        if ($currentIndex !== false && isset($imageIds[$currentIndex - 1])) {
            return Image::find($imageIds[$currentIndex - 1]);
        }

        return null;
    }
}