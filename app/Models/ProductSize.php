<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSize extends Model
{
    public const SIZES = ['Regular', 'Large'];

    public const DEFAULT_MIN_STOCK = 5;

    protected $fillable = [
        'product_id',
        'size_name',
        'base_price',
        'stock',
        'min_stock',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'stock' => 'integer',
            'min_stock' => 'integer',
        ];
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    public function isBelowMinimum(): bool
    {
        return ! $this->isOutOfStock() && $this->stock <= $this->min_stock;
    }

    public function stockStatus(): string
    {
        return $this->isOutOfStock() ? 'habis' : ($this->isBelowMinimum() ? 'menipis' : 'aman');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class, 'product_size_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_size_id');
    }
}
