<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;
    protected $table="attendances";
    protected $fillable = [
        'nik',
        'schedule_group_attendances_id',
        'date',
        'lat_in',
        'lng_in',
        'time_in',
        'photo_in',
        'status_in',
        'lat_out',
        'lng_out',
        'time_out',
        'photo_out',
        'status_out',
        'location',
    ];
    protected $appends = [
        'location',
    ];
    public function location(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => json_encode([
                'lat' => (float) $attributes['lat'],
                'lng' => (float) $attributes['lng'],
            ]),
            set: fn ($value) => [
                'lat' => $value['lat'] ?? null,
                'lng' => $value['lng'] ?? null,
            ],
        );
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ScheduleGroupAttendance::class, 'schedule_group_attendances_id', 'id');
    }
}
