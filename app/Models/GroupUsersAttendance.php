<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUsersAttendance extends Model
{
    use HasFactory;
    protected $table="group_users";
    protected $fillable = [
        'nik',
        'group_attendance_id'
    ];
}
