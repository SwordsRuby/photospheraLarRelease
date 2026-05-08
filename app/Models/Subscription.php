<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'plan',
        'storage_limit',
        'storage_used',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'storage_limit' => 'integer',
        'storage_used' => 'integer',
    ];

    // Subscription plans
    const PLANS = [
        'basic' => [
            'name' => 'Базовый',
            'price' => 0,
            'storage_gb' => 1,
            'duration_months' => null, // indefinite
            'features' => ['private_albums' => true, 'storage_size' => '1 ГБ']
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => 299,
            'storage_gb' => 10,
            'duration_months' => 1,
            'features' => ['private_albums' => true, 'storage_size' => '10 ГБ', 'priority_support' => true]
        ],
        'premium' => [
            'name' => 'Premium',
            'price' => 599,
            'storage_gb' => 50,
            'duration_months' => 1,
            'features' => ['private_albums' => true, 'storage_size' => '50 ГБ', 'priority_support' => true]
        ],
    ];

    /**
     * Get the user that owns the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payments associated with the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if the subscription has expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        if ($this->plan === 'basic') {
            return false;
        }
        return $this->expires_at && Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * Get the remaining storage space in bytes.
     *
     * @return int
     */
    public function getRemainingStorageAttribute()
    {
        return max(0, $this->storage_limit - $this->storage_used);
    }

    /**
     * Get the percentage of storage used.
     *
     * @return float
     */
    public function getStorageUsedPercentageAttribute()
    {
        if ($this->storage_limit == 0) {
            return 0;
        }
        return round(($this->storage_used / $this->storage_limit) * 100, 2);
    }

    /**
     * Get the human-readable plan name.
     *
     * @return string
     */
    public function getPlanNameAttribute()
    {
        return self::PLANS[$this->plan]['name'] ?? 'Базовый';
    }
}