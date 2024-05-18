<?php

namespace App\Models\UserRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UFamily extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'fullname',
        'relationship',
        'birthdate',
        'marital_status',
        'job',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
