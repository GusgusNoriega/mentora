<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PlanCoursePivot extends Pivot
{
    protected $table = 'plan_course';
    public $timestamps = true;
    public $incrementing = false;

    protected $fillable = [
        'plan_id',
        'course_id',
        'created_at',
        'updated_at',
    ];
}