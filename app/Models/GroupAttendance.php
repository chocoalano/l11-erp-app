<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupAttendance extends Model
{
    use HasFactory;
    protected $table="group_attendances";
    protected $fillable = [
        'name',
        'description'
    ];

    public function schedule_attendance(): HasMany
    {
        return $this->hasMany(ScheduleGroupAttendance::class, 'group_attendance_id', 'id');
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'group_users', 'group_attendance_id', 'nik');
    }
}
