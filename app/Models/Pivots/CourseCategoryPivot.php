<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseCategoryPivot extends Pivot
{
    protected $table = 'course_category';
    public $timestamps = true;
    public $incrementing = false;

    protected $fillable = [
        'course_id',
        'category_id',
        'created_at',
        'updated_at',
    ];
}