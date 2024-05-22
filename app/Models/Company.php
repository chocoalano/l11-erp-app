<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserRelated\UEmploye;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="companies";
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'full_address',
    ];
    public function employe(): HasMany
    {
        return $this->hasMany(UEmploye::class, 'company_id', 'id');
    }
}
