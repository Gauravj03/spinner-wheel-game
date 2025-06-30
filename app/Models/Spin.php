<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Spin extends Model
{
    protected $fillable = [
        'user_id',
        'cost',
        'reward',
        'result_label',
    ];

    /**
     * Get the user that performed the spin.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
