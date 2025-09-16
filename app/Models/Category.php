<?php

namespace App\Models;

use App\Models\Pivots\CourseCategoryPivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];

    /**
     * Categoría padre (jerarquía).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Categorías hijas (jerarquía).
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Cursos asociados a esta categoría vía pivote course_category.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_category', 'category_id', 'course_id')
            ->using(CourseCategoryPivot::class)
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