<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizOption extends Model
{
    protected $table = 'quiz_options';

    protected $fillable = [
        'question_id',
        'text',
        'is_correct',
        'weight',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'weight' => 'integer',
    ];

    /**
     * Pregunta a la que pertenece esta opción.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }

    /**
     * Respuestas que seleccionaron esta opción.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'option_id');
    }
}