<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserRelated\UAddres;
use App\Models\UserRelated\UBank;
use App\Models\UserRelated\UBpjs;
use App\Models\UserRelated\UEmergencyContact;
use App\Models\UserRelated\UEmploye;
use App\Models\UserRelated\UFamily;
use App\Models\UserRelated\UFormalEducation;
use App\Models\UserRelated\UInformalEducation;
use App\Models\UserRelated\USalary;
use App\Models\UserRelated\UTaxConfig;
use App\Models\UserRelated\UWorkExperience;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
    use HasRoles;
    use HasPanelShield;
    use HasApiTokens;
    protected $fillable = [
        'name',
        'nik',
        'email',
        'password',
        'phone',
        'placebirth',
        'datebirth',
        'gender',
        'blood',
        'marital_status',
        'religion',
        'image',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function address(): HasOne
    {
        return $this->hasOne(UAddres::class, 'user_id', 'id');
    }
    public function bank(): HasOne
    {
        return $this->hasOne(UBank::class, 'user_id', 'id');
    }
    public function bpjs(): HasOne
    {
        return $this->hasOne(UBpjs::class, 'user_id', 'id');
    }
    public function emergency_contact(): HasMany
    {
        return $this->hasMany(UEmergencyContact::class, 'user_id', 'id');
    }
    public function family(): HasMany
    {
        return $this->hasMany(UFamily::class, 'user_id', 'id');
    }
    public function formal_education(): HasMany
    {
        return $this->hasMany(UFormalEducation::class, 'user_id', 'id');
    }
    public function informal_education(): HasMany
    {
        return $this->hasMany(UInformalEducation::class, 'user_id', 'id');
    }
    public function work_experience(): HasMany
    {
        return $this->hasMany(UWorkExperience::class, 'user_id', 'id');
    }
    public function employe(): HasOne
    {
        return $this->hasOne(UEmploye::class, 'user_id', 'id');
    }
    public function salary(): HasOne
    {
        return $this->hasOne(USalary::class, 'user_id', 'id');
    }
    public function tax_config(): HasOne
    {
        return $this->hasOne(UTaxConfig::class, 'user_id', 'id');
    }
    public function rolefind(){
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }
    public function group_attendance()
    {
        return $this->belongsToMany(GroupAttendance::class, 'group_users', 'user_id', 'group_attendance_id');
    }
}