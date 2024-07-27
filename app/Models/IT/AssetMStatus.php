<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetMStatus extends Model
{
    use HasFactory;
    protected $table="asset_m_status";
    protected $fillable = [
        'name'
    ];
    public function assetManagement(): HasMany
    {
        return $this->hasMany(AssetManagement::class, 'status_id', 'id');
    }
}
