<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSection extends Model
{
    protected $table = 'course_sections';

    protected $fillable = [
        'course_id',
        'title',
        'position',
    ];

    /**
     * Curso al que pertenece la sección.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Lecciones de la sección.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class, 'section_id');
    }

    /**
     * Alcance para ordenar por posición.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}