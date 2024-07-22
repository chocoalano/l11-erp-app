<?php

namespace App\Models\Marketing\Compro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    use HasFactory;
    protected $table = "reasons";
    protected $fillable = [
        'cover_image',
        'title',
        'description',
        'certification_image',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
