<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'login',
        'email',
        'password',
        'img',
        'is_moderator',
        'is_banned',
        'ban_reason',
        'email_verified_at',
        'verification_code',
        'verification_code_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_moderator' => 'boolean',
        'is_banned' => 'boolean',
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'verification_code_expires_at' => 'datetime',
    ];

    /**
     * Get the images uploaded by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(Image::class, 'author_id');
    }

    /**
     * Get the images liked by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->belongsToMany(Image::class, 'likes', 'user_id', 'image_id')
            ->withTimestamps();
    }

    /**
     * Get the images favorited by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorites()
    {
        return $this->belongsToMany(Image::class, 'favorites', 'user_id', 'image_id')
            ->withTimestamps();
    }

    /**
     * Get the albums created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    /**
     * Get the active subscription for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class)->where('is_active', true);
    }

    /**
     * Get the payments made by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if the user has liked a specific image.
     *
     * @param Image $image
     * @return bool
     */
    public function hasLiked(Image $image)
    {
        return $this->likes()->where('image_id', $image->id)->exists();
    }

    /**
     * Check if the user has favorited a specific image.
     *
     * @param Image $image
     * @return bool
     */
    public function hasFavorited(Image $image)
    {
        return $this->favorites()->where('image_id', $image->id)->exists();
    }

    /**
     * Get or create the active subscription for the user.
     *
     * @return Subscription
     */
    public function getActiveSubscriptionAttribute()
    {
        $subscription = $this->subscription()->first();

        if ($subscription && !$subscription->isExpired()) {
            return $subscription;
        }

        if (!$subscription || $subscription->isExpired()) {
            return $this->createBasicSubscription();
        }

        return $subscription;
    }

    /**
     * Create a basic (free) subscription for the user.
     *
     * @return Subscription
     */
    public function createBasicSubscription()
    {
        Subscription::where('user_id', $this->id)->update(['is_active' => false]);

        return Subscription::create([
            'user_id' => $this->id,
            'plan' => 'basic',
            'storage_limit' => 1073741824,
            'storage_used' => 0,
            'expires_at' => null,
            'is_active' => true,
        ]);
    }

    /**
     * Check if the user has enough storage space.
     *
     * @param int $fileSize
     * @return bool
     */
    public function hasStorageSpace($fileSize)
    {
        $subscription = $this->active_subscription;
        return $subscription->remaining_storage >= $fileSize;
    }

    /**
     * Update the storage used by the user.
     *
     * @param int $additionalBytes
     * @return void
     */
    public function updateStorageUsed($additionalBytes)
    {
        $subscription = $this->active_subscription;
        $subscription->update([
            'storage_used' => $subscription->storage_used + $additionalBytes
        ]);
    }

    /**
     * Get the user's private images.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function privateImages()
    {
        return $this->images()->where('is_private', true);
    }

    /**
     * Get the user's public images.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function publicImages()
    {
        return $this->images()->where('is_private', false);
    }

    /**
     * Check if the user's email is verified.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Generate a verification code for email confirmation.
     *
     * @return string
     */
    public function generateVerificationCode()
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->verification_code = $code;
        $this->verification_code_expires_at = now()->addMinutes(15);
        $this->save();

        return $code;
    }

    /**
     * Verify the user's email with the provided code.
     *
     * @param string $code
     * @return bool
     */
    public function verifyCode($code)
    {
        if (
            $this->verification_code === $code &&
            $this->verification_code_expires_at &&
            now()->lessThan($this->verification_code_expires_at)
        ) {

            $this->email_verified_at = now();
            $this->verification_code = null;
            $this->verification_code_expires_at = null;
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Get the ban reason with fallback default.
     *
     * @return string
     */
    public function getBanReasonAttribute($value)
    {
        return $value ?? 'не соблюдение правил сообщества';
    }
}