<?php

namespace App\Models\Marketing\Digital;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;
    protected $table="articles";
    protected $fillable = [
        'user_id',
        'cover_image',
        'title',
        'slug',
        'content',
        'tags',
        'active',
        'meta_descriptions',
        'meta_keywords',
        'meta_title',
    ];

    protected $casts = [
        'tags' => 'array',
        'meta_keywords' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            $article->slug = static::generateSlug($article->title);
        });

        static::updating(function ($article) {
            if ($article->isDirty('title')) {
                $article->slug = static::generateSlug($article->title);
            }
        });
    }

    public static function generateSlug($title)
    {
        return Str::slug($title, '-');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsToMany(ArticleCategory::class, 'article_relation_categories', 'article_id', 'article_category_id');
    }
}
