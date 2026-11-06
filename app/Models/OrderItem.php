<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public const UPDATED_AT = null;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_size_id',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productSize()
    {
        return $this->belongsTo(ProductSize::class);
    }

    public function variants()
    {
        return $this->belongsToMany(Variant::class, 'order_item_variants', 'order_item_id', 'variant_id')
            ->withPivot('variant_price');
    }
}
