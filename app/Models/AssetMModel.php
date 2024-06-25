<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetMModel extends Model
{
    use HasFactory;
    protected $table="asset_m_model";
    protected $fillable = [
        'name',
        'category',
        'types_of_goods',
    ];
    public function assetManagement(): HasMany
    {
        return $this->hasMany(AssetManagement::class, 'model_id', 'id');
    }
}
