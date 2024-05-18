<?php

namespace App\Models\UserRelated;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UBpjs extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'bpjs_ketenagakerjaan',
        'npp_bpjs_ketenagakerjaan',
        'bpjs_ketenagakerjaan_date',
        'bpjs_kesehatan',
        'bpjs_kesehatan_family',
        'bpjs_kesehatan_date',
        'bpjs_kesehatan_cost',
        'jht_cost',
        'jaminan_pensiun_cost',
        'jaminan_pensiun_date',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
