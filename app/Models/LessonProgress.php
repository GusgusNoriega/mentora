<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'progress_pct',
        'seconds_watched',
        'completed_at',
    ];

    protected $casts = [
        'progress_pct' => 'decimal:2',
        'seconds_watched' => 'integer',
        'completed_at' => 'datetime',
    ];

    /**
     * Usuario dueño del progreso.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Lección asociada al progreso.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'lesson_id');
    }
}