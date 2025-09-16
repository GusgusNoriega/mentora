<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponRedemption extends Model
{
    protected $table = 'coupon_redemptions';

    public $timestamps = false; // Solo tiene created_at

    protected $fillable = [
        'coupon_id',
        'user_id',
        'subscription_id',
        'course_id',
        'amount_cents_applied',
        'created_at',
    ];

    protected $casts = [
        'amount_cents_applied' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Cupón canjeado.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    /**
     * Usuario que canjeó el cupón.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Suscripción a la que se aplicó (opcional).
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    /**
     * Curso al que se aplicó (opcional).
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}