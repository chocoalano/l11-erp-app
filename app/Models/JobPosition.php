<?php

namespace App\Models;

use App\Models\UserRelated\UEmploye;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosition extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="job_positions";
    protected $fillable = [
        'name',
        'description'
    ];

    public function employe(): HasMany
    {
        return $this->hasMany(UEmploye::class, 'job_position_id', 'id');
    }
}
