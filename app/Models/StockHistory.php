<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockHistory extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'product_size_id',
        'user_id',
        'type',
        'quantity',
        'source',
        'reference_id',
        'notes',
    ];

    public function productSize(): BelongsTo
    {
        return $this->belongsTo(ProductSize::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
