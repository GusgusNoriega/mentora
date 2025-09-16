<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserSubscription extends Model
{
    protected $table = 'user_subscriptions';

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'started_at',
        'current_period_start',
        'current_period_end',
        'canceled_at',
        'ends_at',
        'provider',
        'provider_sub_id',
        'last_payment_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'canceled_at' => 'datetime',
        'ends_at' => 'datetime',
        'last_payment_at' => 'datetime',
    ];

    /**
     * Usuario dueño de la suscripción.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Plan asociado a la suscripción.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Transacciones de pago relacionadas a esta suscripción.
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'subscription_id');
    }

    /**
     * Canjes de cupones aplicados a esta suscripción.
     */
    public function couponRedemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class, 'subscription_id');
    }

    /**
     * Cupones aplicados a esta suscripción vía coupon_redemptions.
     */
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_redemptions', 'subscription_id', 'coupon_id')
            ->withPivot(['user_id', 'course_id', 'amount_cents_applied', 'created_at']);
    }
}