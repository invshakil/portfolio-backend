<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Article extends BaseModel
{
    use HasFactory;

//    protected $appends = ['image_url', 'thumb_image_url'];

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'meta_description',
        'image',
        'meta_title',
        'slider_status',
        'status',
        'viewed'
    ];

    /**
     * Article Author Relation
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    /**
     * Article Categories Pivot Relation
     * @return BelongsToMany
     */

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'article_categories', 'article_id', 'category_id')->withPivot('id');
    }

    /**
     * Article Tag pivot collection
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tags', 'article_id', 'tag_id')->withPivot('id');
    }
}
