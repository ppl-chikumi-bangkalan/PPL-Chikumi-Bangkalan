<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    public const CATEGORIES = [
        'raw_material' => 'Bahan Baku',
        'packaging' => 'Kemasan',
        'operational' => 'Operasional',
        'salary' => 'Gaji',
        'bonus' => 'Bonus',
        'overtime' => 'Lembur',
        'other' => 'Lainnya',
    ];

    public const PAYMENT_METHODS = [
        'cash' => 'Tunai',
        'transfer' => 'Transfer',
        'qris' => 'QRIS',
    ];

    protected $fillable = [
        'category',
        'title',
        'amount',
        'expense_date',
        'payment_method',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
