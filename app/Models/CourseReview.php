<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseReview extends Model
{
    protected $table = 'course_reviews';

    public $timestamps = false; // Solo created_at en la tabla

    protected $fillable = [
        'course_id',
        'user_id',
        'rating',
        'comment',
        'is_public',
        'created_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_public' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Curso reseñado.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Autor de la reseña.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}