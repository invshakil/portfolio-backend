<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'excerpt',
        'keywords',
        'is_published',
        'is_video',
        'display_in_navigation',
        'display_in_home_page',
        'position',
        'categories',
    ];

    /**
     * @return BelongsToMany
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_categories', 'category_id', 'article_id');
    }
}
