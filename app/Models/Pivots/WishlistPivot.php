<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class WishlistPivot extends Pivot
{
    protected $table = 'wishlists';
    public $incrementing = false;
    public $timestamps = false; // Solo tiene created_at

    protected $fillable = [
        'user_id',
        'course_id',
        'created_at',
    ];
}