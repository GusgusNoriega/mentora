<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'type',
        'amount',
        'max_redemptions',
        'redeemed_count',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'integer',
        'max_redemptions' => 'integer',
        'redeemed_count' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Canjes del cupón.
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class, 'coupon_id');
    }

    /**
     * Usuarios que han canjeado este cupón (vía coupon_redemptions).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_redemptions', 'coupon_id', 'user_id')
            ->withPivot(['subscription_id', 'course_id', 'amount_cents_applied', 'created_at']);
    }

    /**
     * Cursos a los que se aplicó el cupón (vía coupon_redemptions).
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'coupon_redemptions', 'coupon_id', 'course_id')
            ->withPivot(['user_id', 'subscription_id', 'amount_cents_applied', 'created_at']);
    }

    /**
     * Suscripciones a las que se aplicó el cupón (vía coupon_redemptions).
     */
    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(UserSubscription::class, 'coupon_redemptions', 'coupon_id', 'subscription_id')
            ->withPivot(['user_id', 'course_id', 'amount_cents_applied', 'created_at']);
    }
}