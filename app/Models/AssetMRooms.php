<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetMRooms extends Model
{
    use HasFactory;
    protected $table="asset_m_rooms";
    protected $fillable = [
        'name'
    ];
    public function assetManagement(): HasMany
    {
        return $this->hasMany(AssetManagement::class, 'room_id', 'id');
    }
}
