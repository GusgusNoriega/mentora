<?php

namespace App\Models;

use App\Models\Pivots\CourseTagPivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $table = 'tags';

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Cursos asociados a esta etiqueta vía pivote course_tag.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_tag', 'tag_id', 'course_id')
            ->using(CourseTagPivot::class)
            ->withTimestamps();
    }

    /**
     * Alcance para ordenar por nombre.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}