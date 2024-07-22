<?php

namespace App\Models\Marketing\Compro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sosmed extends Model
{
    use HasFactory;
    protected $table = "sosmeds";
    protected $fillable = [
        'name',
        'url',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
