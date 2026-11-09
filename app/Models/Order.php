<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'customer_id',
        'order_type',
        'dining_option',
        'order_status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'pickup_date',
        'pickup_time',
        'notes',
        'subtotal',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'subtotal' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
