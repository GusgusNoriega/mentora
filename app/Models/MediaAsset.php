<?php

namespace App\Models;

use App\Models\Pivots\LessonMediaPivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MediaAsset extends Model
{
    protected $table = 'media_assets';

    public $timestamps = false; // Solo created_at

    protected $fillable = [
        'owner_id',
        'type',
        'provider',
        'url',
        'storage_path',
        'mime_type',
        'size_bytes',
        'duration_seconds',
        'created_at',
        'name',
        'alt',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'duration_seconds' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Propietario del recurso (users.id).
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Lecciones que referencian este medio vía pivote lesson_media.
     */
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(CourseLesson::class, 'lesson_media', 'media_id', 'lesson_id')
            ->using(LessonMediaPivot::class);
    }
}