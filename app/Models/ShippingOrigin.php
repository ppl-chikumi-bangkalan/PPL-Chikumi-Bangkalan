<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingOrigin extends Model
{
    protected $fillable = ['name', 'address', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
