<?php

namespace App\Models\Marketing\Digital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleRelationCategory extends Model
{
    use HasFactory;
    protected $table = 'article_relation_categories';
    public $timestamps = false; // Jika tidak membutuhkan kolom timestamps

    protected $primaryKey = ['article_id', 'article_category_id']; // Menggunakan array karena primary key gabungan

    // Inisialisasi primary key jika bukan auto increment
    public $incrementing = false;

    // Atur tipe data primary key
    protected $keyType = 'string';

    // Relasi dengan model Article
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

    // Relasi dengan model ArticleCategory
    public function articleCategory()
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id', 'id');
    }
}
