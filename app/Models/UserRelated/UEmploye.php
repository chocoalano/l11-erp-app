<?php

namespace App\Models\UserRelated;

use App\Models\Branch;
use App\Models\Company;
use App\Models\JobLevel;
use App\Models\JobPosition;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UEmploye extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'organization_id',
        'job_position_id',
        'job_level_id',
        'approval_line',
        'approval_manager',
        'company_id',
        'branch_id',
        'status',
        'join_date',
        'sign_date',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
    public function job_position(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'job_position_id', 'id');
    }
    public function job_level(): BelongsTo
    {
        return $this->belongsTo(JobLevel::class, 'job_level_id', 'id');
    }
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    public function approval_line(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_line', 'id');
    }
    public function approval_manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_manager', 'id');
    }
}
