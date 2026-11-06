<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $table = 'delivery_addresses';

    protected $fillable = ['customer_id', 'label', 'recipient_name', 'phone', 'detail', 'city', 'postal_code'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
