<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonField extends Model
{
    protected $fillable = [
        'lesson_id',
        'field_type',
        'field_key',
        'field_value',
        'media_id',
        'position',
    ];

    // Relación con la lección
    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'lesson_id');
    }

    // Relación con el media asset (opcional)
    public function media()
    {
        return $this->belongsTo(MediaAsset::class, 'media_id');
    }

    // Scope para ordenar por position
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}
