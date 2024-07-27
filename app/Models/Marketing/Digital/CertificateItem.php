<?php

namespace App\Models\Marketing\Digital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateItem extends Model
{
    use HasFactory;
    protected $table="certificate_items";
    protected $fillable = [
        'certificate_id',
        'cover_image',
        'title',
        'description',
        'active',
    ];

    public function certificate()
    {
        return $this->belongsTo(Certificate::class, 'certificate_id', 'id');
    }
}
