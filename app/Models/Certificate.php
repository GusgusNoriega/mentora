<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $table = 'certificates';

    protected $fillable = [
        'user_id',
        'course_id',
        'template_id',
        'code',
        'issued_at',
        'grade',
        'expires_at',
        'public_url',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'grade' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    /**
     * Usuario al que se emitió el certificado.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Curso por el cual se emite el certificado.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Plantilla usada para generar el certificado.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }
}