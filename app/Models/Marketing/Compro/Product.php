<?php

namespace App\Models\Marketing\Compro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'cover_image',
        'title',
        'slug',
        'description',
        'active',
        'meta_descriptions',
        'meta_keywords',
        'meta_title',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function child()
    {
        return $this->hasMany(ProductItem::class, 'product_id', 'id');
    }
}
