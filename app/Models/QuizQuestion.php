<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    protected $table = 'quiz_questions';

    protected $fillable = [
        'quiz_id',
        'type',
        'text',
        'points',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    /**
     * Quiz al que pertenece la pregunta.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    /**
     * Opciones de la pregunta.
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class, 'question_id');
    }

    /**
     * Respuestas registradas para esta pregunta.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'question_id');
    }
}