<?php

namespace App\Models\HRGA;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispen extends Model
{
    use HasFactory;
    protected $table = 'dispens';
    protected $fillable = [
        'event_number',
        'user_id',
        'category',
        'start_date',
        'end_date',
        'total_day',
        'description',
        'user_approved',
        'line_approved',
        'hrga_approved',
    ];
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
