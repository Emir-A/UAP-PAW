<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableEntry extends Model
{
    protected $fillable = [
        'user_id',
        'course_session_id',
    ];

    public function courseSession(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class);
    }
}
