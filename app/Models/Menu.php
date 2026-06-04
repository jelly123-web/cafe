<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_category_id',
        'code',
        'barcode',
        'name',
        'selling_price',
        'cost_price',
        'image_path',
        'is_sold_out',
    ];

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'is_sold_out' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleTransactionItem::class);
    }

    public function foodPackages(): BelongsToMany
    {
        return $this->belongsToMany(FoodPackage::class, 'food_package_menu')->withTimestamps();
    }

    public function promos(): BelongsToMany
    {
        return $this->belongsToMany(Promo::class, 'promo_menu');
    }
}
