<?php

namespace App\Models;

use App\Models\UserRelated\UFormalEducation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeEducation extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="grades";
    protected $fillable = [
        'name'
    ];
    public function education(): HasMany
    {
        return $this->hasMany(UFormalEducation::class, 'branch_id', 'id');
    }
}
