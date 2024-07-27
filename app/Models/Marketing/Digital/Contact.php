<?php

namespace App\Models\Marketing\Digital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $table = "contacts";
    protected $fillable = [
        'name',
        'hp',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
