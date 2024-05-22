<?php

namespace App\Models;

use App\Models\UserRelated\UEmploye;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="organizations";
    protected $fillable = [
        'name',
        'description'
    ];

    public function employe(): HasMany
    {
        return $this->hasMany(UEmploye::class, 'organization_id', 'id');
    }
}
