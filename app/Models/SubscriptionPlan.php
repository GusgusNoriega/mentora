<?php

namespace App\Models;

use App\Models\Pivots\PlanCoursePivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $table = 'subscription_plans';

    protected $fillable = [
        'name',
        'description',
        'price_cents',
        'currency',
        'interval',
        'trial_days',
        'access_all_courses',
        'is_active',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'trial_days' => 'integer',
        'access_all_courses' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Cursos incluidos en este plan (vía pivote plan_course).
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'plan_course', 'plan_id', 'course_id')
            ->using(PlanCoursePivot::class)
            ->withTimestamps();
    }

    /**
     * Registros de suscripciones de usuarios a este plan.
     */
    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }

    /**
     * Usuarios suscritos a este plan (vía pivote user_subscriptions).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_subscriptions', 'plan_id', 'user_id')
            ->withPivot([
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
            ]);
    }
}