<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $table = 'images';

    protected $fillable = [
        'img',
        'name',
        'category_id',
        'author_id',
        'is_approved',
        'is_private',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_private' => 'boolean',
    ];

    /**
     * Get the category that owns the image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the author (user) of the image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the tags associated with the image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'image_tag', 'image_id', 'tag_id')
            ->withTimestamps();
    }

    /**
     * Get the users who liked this image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes', 'image_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Get the users who favorited this image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'image_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Get the albums containing this image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function albums()
    {
        return $this->belongsToMany(Album::class, 'album_image', 'image_id', 'album_id')
            ->withTimestamps();
    }

    /**
     * Get the like count for this image.
     *
     * @return int
     */
    public function getLikeCountAttribute()
    {
        return $this->likes()->count();
    }

    /**
     * Check if the current user has liked this image.
     *
     * @return bool
     */
    public function getIsLikedByUserAttribute()
    {
        if (auth()->check()) {
            return $this->likes()->where('user_id', auth()->id())->exists();
        }
        return false;
    }

    /**
     * Check if the current user has favorited this image.
     *
     * @return bool
     */
    public function getIsFavoritedByUserAttribute()
    {
        if (auth()->check()) {
            return $this->favorites()->where('user_id', auth()->id())->exists();
        }
        return false;
    }

    /**
     * Scope a query to only approved images.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only pending images.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }
}