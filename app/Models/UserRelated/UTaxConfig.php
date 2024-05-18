<?php

namespace App\Models\UserRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UTaxConfig extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'npwp_15_digit_old',
        'npwp_16_digit_new',
        'ptkp_status',
        'tax_method',
        'tax_salary',
        'emp_tax_status',
        'beginning_netto',
        'pph21_paid',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
