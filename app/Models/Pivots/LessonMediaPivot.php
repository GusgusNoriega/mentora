<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LessonMediaPivot extends Pivot
{
    protected $table = 'lesson_media';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'lesson_id',
        'media_id',
    ];
}