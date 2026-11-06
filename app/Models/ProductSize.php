<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSize extends Model
{
    public const SIZES = ['Regular', 'Large'];

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
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class, 'product_size_id');
    }
}
