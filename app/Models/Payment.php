<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'payment_method',
        'payment_status',
        'amount',
        'paid_at',
        'provider',
        'transaction_reference',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
