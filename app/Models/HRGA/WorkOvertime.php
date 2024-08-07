<?php

namespace App\Models\HRGA;

use App\Models\JobPosition;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOvertime extends Model
{
    use HasFactory;
    protected $table = 'work_overtime';

    protected $fillable = [
        'event_number',
        'userid_created',
        'date_spl',
        'organization_id',
        'job_position_id',
        'overtime_day_status',
        'date_overtime_at',
        'admin_approved',
        'line_approved',
        'gm_approved',
        'hrga_approved',
        'director_approved',
        'fat_approved',
    ];
    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_number', 'event_number');
    }

    public function userCreated()
    {
        return $this->belongsTo(User::class, 'userid_created', 'id');
    }
    public function userMember()
    {
        return $this->hasMany(UserOvertime::class, 'work_overtime_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function jobPosition()
    {
        return $this->belongsTo(JobPosition::class, 'job_position_id', 'id');
    }

    public function getAdminApprovedAttribute($value)
    {
        return $value == 'y' ? 'Yes' : ($value == 'n' ? 'No' : 'Waiting');
    }
    public function getLineApprovedAttribute($value)
    {
        return $value == 'y' ? 'Yes' : ($value == 'n' ? 'No' : 'Waiting');
    }
    public function getGmApprovedAttribute($value)
    {
        return $value == 'y' ? 'Yes' : ($value == 'n' ? 'No' : 'Waiting');
    }
    public function getHrgaApprovedAttribute($value)
    {
        return $value == 'y' ? 'Yes' : ($value == 'n' ? 'No' : 'Waiting');
    }
    public function getDirectorApprovedAttribute($value)
    {
        return $value == 'y' ? 'Yes' : ($value == 'n' ? 'No' : 'Waiting');
    }
    public function getFatApprovedAttribute($value)
    {
        return $value == 'y' ? 'Yes' : ($value == 'n' ? 'No' : 'Waiting');
    }
}
