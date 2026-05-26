<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'branch_id',
        'table_id',
        'sold_at',
        'total_amount',
        'total_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sold_at' => 'datetime',
            'total_amount' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(DiningTable::class, 'table_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleTransactionItem::class);
    }
}
