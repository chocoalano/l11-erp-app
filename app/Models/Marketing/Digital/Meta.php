<?php

namespace App\Models\Marketing\Digital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasFactory;
    protected $fillable = [
        'page',
        'meta_descriptions',
        'meta_keywords',
        'meta_title',
        'meta_type',
        'meta_site_name',
        'meta_image',
        'active',
    ];

    protected $casts = [
        'meta_descriptions' => 'string',
        'meta_keywords' => 'array',
        'meta_title' => 'string',
        'meta_type' => 'string',
        'meta_site_name' => 'string',
        'meta_image' => 'string',
        'active' => 'boolean',
    ];
}
