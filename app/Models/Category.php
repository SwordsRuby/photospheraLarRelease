<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'img',
    ];

    /**
     * Get the images belonging to this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    protected static function booted()
    {
        static::deleting(function ($category) {
            Image::where('category_id', $category->id)
                ->update(['category_id' => 1]);
        });
    }
}