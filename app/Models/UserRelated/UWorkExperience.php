<?php

namespace App\Models\UserRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UWorkExperience extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'company',
        'position',
        'from',
        'to',
        'length_of_service',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
