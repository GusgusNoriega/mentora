<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $table = 'quizzes';

    protected $fillable = [
        'lesson_id',
        'title',
        'passing_score',
        'attempts_allowed',
        'time_limit_minutes',
    ];

    protected $casts = [
        'passing_score' => 'integer',
        'attempts_allowed' => 'integer',
        'time_limit_minutes' => 'integer',
    ];

    /**
     * Lección a la que pertenece el quiz.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'lesson_id');
    }

    /**
     * Preguntas del quiz.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id');
    }

    /**
     * Intentos realizados por los usuarios.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }
}