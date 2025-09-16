<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';

    public $timestamps = false; // Solo created_at en la tabla

    protected $fillable = [
        'user_id',
        'subscription_id',
        'course_id',
        'amount_cents',
        'currency',
        'status',
        'provider',
        'provider_payment_id',
        'receipt_url',
        'created_at',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Usuario que realizó el pago.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Suscripción asociada (opcional).
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    /**
     * Curso asociado (opcional cuando es compra única).
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}