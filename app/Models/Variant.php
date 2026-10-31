<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Variant extends Model
{
    public const TYPES = ['filling', 'dipping_sauce', 'seasoning'];

    public const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'type',
        'name',
        'additional_price',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'additional_price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
