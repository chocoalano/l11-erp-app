<?php

namespace App\Models\UserRelated;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UEmergencyContact extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'relationship',
        'phone',
        'profesion',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
