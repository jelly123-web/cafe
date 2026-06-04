<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_category_id',
        'barcode',
        'name',
        'type',
        'unit',
        'stock',
        'min_stock',
        'stock_good',
        'stock_less_good',
        'stock_damaged',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'decimal:2',
            'min_stock' => 'decimal:2',
            'stock_good' => 'decimal:2',
            'stock_less_good' => 'decimal:2',
            'stock_damaged' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getTotalStockAttribute(): float
    {
        return (float) $this->stock_good + (float) $this->stock_less_good + (float) $this->stock_damaged;
    }
}
