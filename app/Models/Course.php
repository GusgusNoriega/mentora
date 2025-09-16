<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\Pivots\CourseInstructorPivot;
use App\Models\Pivots\CourseCategoryPivot;
use App\Models\Pivots\CourseTagPivot;
use App\Models\Pivots\PlanCoursePivot;
use App\Models\Pivots\WishlistPivot;

class Course extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'thumbnail_url',
        'level',
        'language',
        'status',
        'access_mode',
        'price_cents',
        'currency',
        'created_by',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'price_cents' => 'integer',
    ];

    /**
     * Usuario creador (courses.created_by -> users.id)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Secciones del curso.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class, 'course_id');
    }

    /**
     * Lecciones del curso a través de las secciones.
     */
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(
            CourseLesson::class,
            CourseSection::class,
            'course_id',
            'section_id',
            'id',
            'id'
        );
    }

    /**
     * Instructores vía pivote course_instructors.
     */
    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_instructors', 'course_id', 'user_id')
            ->using(CourseInstructorPivot::class)
            ->withPivot(['is_primary'])
            ->withTimestamps();
    }

    /**
     * Categorías vía pivote course_category.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'course_category', 'course_id', 'category_id')
            ->using(CourseCategoryPivot::class)
            ->withTimestamps();
    }

    /**
     * Tags vía pivote course_tag.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'course_tag', 'course_id', 'tag_id')
            ->using(CourseTagPivot::class)
            ->withTimestamps();
    }

    /**
     * Planes que incluyen este curso vía plan_course.
     */
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'plan_course', 'course_id', 'plan_id')
            ->using(PlanCoursePivot::class)
            ->withTimestamps();
    }

    /**
     * Estudiantes inscritos vía enrollments (tiene metacampos y timestamps).
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'user_id')
            ->withPivot(['source', 'enrolled_at', 'expires_at'])
            ->withTimestamps();
    }

    /**
     * Registros de inscripción.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'course_id');
    }

    /**
     * Progreso de curso.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class, 'course_id');
    }

    /**
     * Reseñas del curso.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class, 'course_id');
    }

    /**
     * Certificados emitidos sobre este curso.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'course_id');
    }

    /**
     * Transacciones asociadas a compras de este curso.
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'course_id');
    }

    /**
     * Usuarios que marcaron este curso en wishlist (sin timestamps).
     */
    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists', 'course_id', 'user_id')
            ->using(WishlistPivot::class)
            ->withPivot(['created_at']);
    }

    /**
     * Cupones aplicados a este curso vía coupon_redemptions.
     */
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_redemptions', 'course_id', 'coupon_id');
    }
}