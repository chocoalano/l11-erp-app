<?php

namespace App\Models\HRGA;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustAttendance extends Model
{
    use HasFactory;
    // Nama tabel
    protected $table = 'adjust_attendance';

    // Kolom-kolom yang dapat diisi
    protected $fillable = [
        'event_number',
        'user_id',
        'problem',
        'date',
        'description',
        'user_approved',
        'line_approved',
        'hrga_approved',
    ];

    // Nilai default untuk kolom enum
    protected $attributes = [
        'user_approved' => 'w',
        'line_approved' => 'w',
        'hrga_approved' => 'w',
    ];

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_number', 'event_number');
    }
    public function getUserApprovedAttribute($value)
    {
        return $value == 'y' ? 'Yes' : ($value == 'n' ? 'No' : 'Waiting');
    }

    public function getLineApprovedAttribute($value)
    {
        return $value == 'y' ? 'Yes' : ($value == 'n' ? 'No' : 'Waiting');
    }

    public function getHrgaApprovedAttribute($value)
    {
        return $value == 'y' ? 'Yes' : ($value == 'n' ? 'No' : 'Waiting');
    }
}
