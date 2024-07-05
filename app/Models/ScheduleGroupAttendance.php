<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleGroupAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_attendance_id',
        'time_attendance_id',
        'date'
    ];

    public function time(): BelongsTo
    {
        return $this->belongsTo(TimeAttendance::class, 'time_attendance_id', 'id');
    }
    public function group_attendance(): BelongsTo
    {
        return $this->belongsTo(GroupAttendance::class, 'group_attendance_id', 'id');
    }
    public function group_users(): BelongsTo
    {
        return $this->belongsTo(GroupUsersAttendance::class, 'group_attendance_id', 'group_attendance_id');
    }
}
