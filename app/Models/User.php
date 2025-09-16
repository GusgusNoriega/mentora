<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // En Laravel 12 puedes usar el cast "hashed" para hashear al asignar:
        'password' => 'hashed',
    ];

    /**
     * Cursos creados por el usuario (courses.created_by -> users.id).
     */
    public function createdCourses(): HasMany
    {
        return $this->hasMany(\App\Models\Course::class, 'created_by');
    }

    /**
     * Cursos donde el usuario es instructor via tabla pivote course_instructors.
     * Pivot: is_primary + timestamps.
     */
    public function instructorCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Course::class,
            'course_instructors',
            'user_id',
            'course_id'
        )->using(\App\Models\Pivots\CourseInstructorPivot::class)
         ->withPivot('is_primary')
         ->withTimestamps();
    }

    /**
     * Cursos en los que el usuario está inscrito via tabla pivote enrollments.
     * Pivot: source, enrolled_at, expires_at + timestamps.
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Course::class,
            'enrollments',
            'user_id',
            'course_id'
        )->withPivot(['source', 'enrolled_at', 'expires_at'])
         ->withTimestamps();
    }

    /**
     * Progreso de lecciones (pivot lesson_progress).
     * Pivot: progress_pct, seconds_watched, completed_at + timestamps.
     */
    public function lessonsProgress(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\CourseLesson::class,
            'lesson_progress',
            'user_id',
            'lesson_id'
        )->withPivot(['progress_pct', 'seconds_watched', 'completed_at'])
         ->withTimestamps();
    }

    /**
     * Progreso de cursos (pivot course_progress).
     * Pivot: progress_pct, completed_at + timestamps.
     */
    public function coursesProgress(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Course::class,
            'course_progress',
            'user_id',
            'course_id'
        )->withPivot(['progress_pct', 'completed_at'])
         ->withTimestamps();
    }

    /**
     * Registros de suscripciones del usuario (user_subscriptions).
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(\App\Models\UserSubscription::class, 'user_id');
    }

    /**
     * Planes de suscripción del usuario via pivote user_subscriptions.
     * Pivot incluye metacampos y timestamps.
     */
    public function subscriptionPlans(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\SubscriptionPlan::class,
            'user_subscriptions',
            'user_id',
            'plan_id'
        )->withPivot([
            'id',
            'status',
            'started_at',
            'current_period_start',
            'current_period_end',
            'canceled_at',
            'ends_at',
            'provider',
            'provider_sub_id',
            'last_payment_at',
            'created_at',
            'updated_at',
        ])->withTimestamps();
    }

    /**
     * Transacciones de pago del usuario.
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(\App\Models\PaymentTransaction::class, 'user_id');
    }

    /**
     * Cupones canjeados por el usuario via pivote coupon_redemptions.
     * Nota: Solo tiene created_at; no usar withTimestamps().
     */
    public function redeemedCoupons(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Coupon::class,
            'coupon_redemptions',
            'user_id',
            'coupon_id'
        )->withPivot(['subscription_id', 'course_id', 'amount_cents_applied', 'created_at']);
    }

    /**
     * Cursos en la lista de deseos del usuario via pivote wishlists.
     * Nota: Solo created_at; no usar withTimestamps().
     */
    public function wishlistCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Course::class,
            'wishlists',
            'user_id',
            'course_id'
        )->using(\App\Models\Pivots\WishlistPivot::class)
         ->withPivot(['created_at']);
    }

    /**
     * Certificados emitidos al usuario.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(\App\Models\Certificate::class, 'user_id');
    }

    /**
     * Plantillas de certificado creadas por el usuario (certificate_templates.created_by).
     */
    public function createdCertificateTemplates(): HasMany
    {
        return $this->hasMany(\App\Models\CertificateTemplate::class, 'created_by');
    }

    /**
     * Intentos de quiz del usuario.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(\App\Models\QuizAttempt::class, 'user_id');
    }

    /**
     * Reseñas de cursos creadas por el usuario.
     */
    public function courseReviews(): HasMany
    {
        return $this->hasMany(\App\Models\CourseReview::class, 'user_id');
    }

    /**
     * Recursos multimedia propiedad del usuario.
     */
    public function mediaAssets(): HasMany
    {
        return $this->hasMany(\App\Models\MediaAsset::class, 'owner_id');
    }
}