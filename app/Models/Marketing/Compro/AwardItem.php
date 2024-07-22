<?php

namespace App\Models\Marketing\Compro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwardItem extends Model
{
    use HasFactory;
    protected $table="award_items";
    protected $fillable = [
        'award_id',
        'cover_image',
        'title',
        'description',
        'active',
    ];

    public function award()
    {
        return $this->belongsTo(Award::class);
    }
}
