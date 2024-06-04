<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutAttendance extends Model
{
    use HasFactory;
    protected $table="out_attendances";
    protected $fillable = [
        'in_attendance_id',
        'nik',
        'schedule_group_attendances_id',
        'lat',
        'lng',
        'date',
        'time',
        'photo',
        'status',
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
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ScheduleGroupAttendance::class, 'schedule_group_attendances_id', 'id');
    }
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(InAttendance::class, 'in_attendance_id', 'id');
    }
}
