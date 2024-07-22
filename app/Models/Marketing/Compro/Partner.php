<?php

namespace App\Models\Marketing\Compro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;
    protected $table = "partners";
    protected $fillable = [
        'image',
        'video',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
