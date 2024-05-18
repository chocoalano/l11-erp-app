<?php

namespace App\Models\SystemSetup;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserRelated\UEmploye;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="branches";
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'full_address',
    ];
    public function employe(): HasMany
    {
        return $this->hasMany(UEmploye::class, 'branch_id', 'id');
    }
}
