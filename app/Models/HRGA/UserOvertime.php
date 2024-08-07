<?php

namespace App\Models\HRGA;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOvertime extends Model
{
    use HasFactory;
    protected $table = 'user_overtime';

    protected $fillable = [
        'work_overtime_id',
        'user_id',
    ];

    public function workOvertime()
    {
        return $this->belongsTo(WorkOvertime::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
