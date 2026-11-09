<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    protected $fillable = [
        'product_code',
        'name',
        'price',
        'category',
        'variant_group',
        'size_label',
        'image_url',
        'stock',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function getImageSrcAttribute(): ?string
    {
        if (!$this->image_url) {
            return null;
        }

        if (Str::startsWith($this->image_url, 'products/')) {
            return Storage::disk('public')->exists($this->image_url)
                ? Storage::disk('public')->url($this->image_url)
                : null;
        }

        return is_file(public_path($this->image_url))
            ? asset($this->image_url)
            : null;
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
