<?php

namespace App\Models\UserRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UInformalEducation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'start',
        'finish',
        'expired',
        'type',
        'duration',
        'fee',
        'description',
        'certification',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
