<?php

namespace App\Models\Marketing\Compro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    use HasFactory;
    protected $table = "carousels";
    protected $fillable = [
        'image',
        'active',
    ];
}
