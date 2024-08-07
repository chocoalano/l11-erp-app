<?php

namespace App\Models\HRGA;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinInOut extends Model
{
    use HasFactory;
    protected $table = 'izins_in_out';

    protected $fillable = [
        'event_number',
        'user_id',
        'date',
        'out_time',
        'in_time',
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

    public function getCategoryAttribute($value)
    {
        return ucfirst($value);
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
