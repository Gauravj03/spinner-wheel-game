<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class BalanceLog extends Model
{
     use HasFactory;

   protected $fillable = [
        'user_id',
        'amount',
        'type',
    ];

    /**
     * Get the user that owns the balance log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
}
