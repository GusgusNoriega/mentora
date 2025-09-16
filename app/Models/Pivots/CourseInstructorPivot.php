<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseInstructorPivot extends Pivot
{
    protected $table = 'course_instructors';
    public $timestamps = true;
    public $incrementing = false;

    protected $fillable = [
        'course_id',
        'user_id',
        'is_primary',
        'created_at',
        'updated_at',
    ];
}