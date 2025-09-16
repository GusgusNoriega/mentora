<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseTagPivot extends Pivot
{
    protected $table = 'course_tag';
    public $timestamps = true;
    public $incrementing = false;

    protected $fillable = [
        'course_id',
        'tag_id',
        'created_at',
        'updated_at',
    ];
}