<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeAttendance extends Model
{
    use HasFactory;

    protected $table="time_attendances";
    protected $fillable = [
        'type',
        'in',
        'out'
    ];

    public function schedule_group_attendance(): HasMany
    {
        return $this->hasMany(ScheduleGroupAttendance::class, 'time_attendance_id', 'id');
    }
}
