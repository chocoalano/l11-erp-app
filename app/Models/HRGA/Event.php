<?php

namespace App\Models\HRGA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;
    // Nama tabel di database
    protected $table = 'events';

    // Kolom-kolom yang dapat diisi
    protected $fillable = [
        'event_number',
        'model_name',
        'id_form',
    ];

    // Menentukan kolom yang akan di-cast
    protected $casts = [
        'id_form' => 'integer',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->event_number = static::generateUniqueCode();
        });
    }

    public static function generateUniqueCode()
    {
        $code = strtoupper(Str::random(8));
        while (self::where('event_number', $code)->exists()) {
            $code = strtoupper(Str::random(8));
        }
        return $code;
    }
    public function cuti()
    {
        return $this->hasOne(Cuti::class, 'id', 'id_form');
    }
}
