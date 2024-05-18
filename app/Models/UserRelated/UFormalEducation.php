<?php

namespace App\Models\UserRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UFormalEducation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'grade_id',
        'institution',
        'majors',
        'score',
        'start',
        'finish',
        'description',
        'certification',
        'file',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
    public function grade(): BelongsTo
    {
        return $this->belongsTo(\App\Models\SystemSetup\GradeEducation::class, 'grade_id', 'id');
    }
}
