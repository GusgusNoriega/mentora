<?php

namespace App\Models;

use App\Models\Pivots\LessonMediaPivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CourseLesson extends Model
{
    protected $table = 'course_lessons';

    protected $fillable = [
        'section_id',
        'title',
        'content_type',
        'content_url',
        'duration_seconds',
        'is_preview',
        'position',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'is_preview' => 'boolean',
    ];

    /**
     * Sección a la que pertenece la lección.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    /**
     * Quiz asociado (1:1).
     */
    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class, 'lesson_id');
    }

    /**
     * Usuarios con progreso en esta lección (pivot: lesson_progress).
     * Pivot: progress_pct, seconds_watched, completed_at + timestamps.
     */
    public function usersProgress(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lesson_progress', 'lesson_id', 'user_id')
            ->withPivot(['progress_pct', 'seconds_watched', 'completed_at'])
            ->withTimestamps();
    }

    /**
     * Registros de progreso (modelo explícito).
     */
    public function progressRecords(): HasMany
    {
        return $this->hasMany(LessonProgress::class, 'lesson_id');
    }

    /**
     * Medios asociados vía pivote lesson_media (sin timestamps).
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(MediaAsset::class, 'lesson_media', 'lesson_id', 'media_id')
            ->using(LessonMediaPivot::class);
    }

    /**
     * Alcance para ordenar por posición.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}