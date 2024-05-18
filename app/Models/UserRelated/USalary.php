<?php

namespace App\Models\UserRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class USalary extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'basic_salary',
        'salary_type',
        'payment_schedule',
        'prorate_settings',
        'overtime_settings',
        'cost_center',
        'cost_center_category',
        'currency',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
