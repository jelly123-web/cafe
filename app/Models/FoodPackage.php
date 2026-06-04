<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FoodPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'selling_price',
        'cost_price',
        'image_path',
        'notes',
        'free_item',
        'menu_category_id',
    ];

    public function category()
    {
        return $this->belongsTo(\App\Models\MenuCategory::class, 'menu_category_id');
    }

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
        ];
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'food_package_menu')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
