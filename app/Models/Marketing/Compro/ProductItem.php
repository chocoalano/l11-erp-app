<?php

namespace App\Models\Marketing\Compro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    use HasFactory;
    protected $table = "product_items";
    protected $fillable = [
        'product_id',
        'cover_image',
        'title',
        'slug',
        'description',
        'active',
        'meta_descriptions',
        'meta_keywords',
        'meta_title',
    ];

    /**
     * Get the product that owns the product item.
     */
    public function parent()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
