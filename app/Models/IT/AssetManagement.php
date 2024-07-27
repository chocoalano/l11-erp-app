<?php

namespace App\Models\IT;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetManagement extends Model
{
    use HasFactory;
    protected $table="asset_management";
    protected $fillable = [
        'company_id',
        'asset_tag',
        'model_id',
        'status_id',
        'room_id',
        'pic',
        'notes',
        'image',
        'purchase_at',
        'purchase_price',
        'suppliers',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    public function model(): BelongsTo
    {
        return $this->belongsTo(AssetMModel::class, 'model_id', 'id');
    }
    public function status(): BelongsTo
    {
        return $this->belongsTo(AssetMStatus::class, 'status_id', 'id');
    }
    public function room(): BelongsTo
    {
        return $this->belongsTo(AssetMRooms::class, 'room_id', 'id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic', 'id');
    }

    public static function generateUniqueCode()
    {
        $lastCode = static::orderBy('created_at', 'desc')->pluck('asset_tag')->first();

        // Extract the YYYYMM part from the last code
        $lastCodeMonth = substr($lastCode, 4, 2);
        $lastCodeYear = substr($lastCode, 0, 4);

        // Get the current month and year
        $currentMonth = now()->format('m');
        $currentYear = now()->format('Y');

        // Reset increment if it's a new month or year
        if ($lastCodeYear != $currentYear || $lastCodeMonth != $currentMonth) {
            $increment = 1;
        } else {
            $increment = (int) substr($lastCode, 6) + 1;
        }

        // Format the code as YYYYMM(increment)
        $newCode = sprintf('%04d%02d%03d', $currentYear, $currentMonth, $increment);

        return $newCode;
    }
}
