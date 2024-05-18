<?php

namespace App\Models\HumanResources\Permit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "permit_presence_from_outside_offices";
    protected $casts = [
        'clockin_latlong' => 'array',
        'clockout_latlong' => 'array',
    ];
    protected $label = [
        'user_id',
        'clockin_time',
        'clockin_latlong',
        'clockin_image',
        'clockin_status',
        'clockin_remark',
        'clockin_approve_line',
        'clockin_approve_manager',
        'clockin_approve_hr_manager',
        'clockout_time',
        'clockout_latlong',
        'clockout_image',
        'clockout_status',
        'clockout_remark',
        'clockout_approve_line',
        'clockout_approve_manager',
        'clockout_approve_hr_manager',
    ];
    public function user(): HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }
}
