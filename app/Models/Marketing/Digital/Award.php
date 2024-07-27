<?php

namespace App\Models\Marketing\Digital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    use HasFactory;
    protected $table = "awards";
    protected $fillable = [
        'cover_image',
        'title',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function item()
    {
        return $this->hasMany(AwardItem::class);
    }
}
