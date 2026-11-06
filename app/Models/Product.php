<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'image_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    // Bentuk data katalog/detail untuk pelanggan.
    public function toCustomerArray(): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->name,
            'deskripsi' => $this->description,
            'gambar' => $this->image_url,
            'kategori' => $this->category?->name,
            'ukuran' => $this->sizes->map(fn (ProductSize $size) => [
                'id' => $size->id,
                'nama' => $size->size_name,
                'harga_dasar' => (float) $size->base_price,
                'tersedia' => $size->stock > 0,
            ])->values(),
            'varian' => $this->variants
                ->where('is_available', true)
                ->groupBy('type')
                ->map(fn ($group, $type) => [
                    'type' => $type,
                    'pilihan' => $group->map(fn (Variant $v) => [
                        'id' => $v->id,
                        'nama' => $v->name,
                        'tambahan_harga' => (float) $v->additional_price,
                    ])->values(),
                ])->values(),
            'tersedia' => $this->sizes->contains(fn (ProductSize $size) => $size->stock > 0),
        ];
    }
}
