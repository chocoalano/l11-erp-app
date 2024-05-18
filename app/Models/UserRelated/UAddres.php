<?php

namespace App\Models\UserRelated;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UAddres extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'idtype',
        'idnumber',
        'idexpired',
        'ispermanent',
        'postalcode',
        'citizen_id_address',
        'use_as_residential',
        'residential_address',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
