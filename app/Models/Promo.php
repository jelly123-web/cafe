<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'applies_to',
        'value',
        'min_spend',
        'buy_qty',
        'get_qty',
        'is_active',
        'banner_path',
        'start_at',
        'end_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_spend' => 'decimal:2',
            'buy_qty' => 'integer',
            'get_qty' => 'integer',
            'is_active' => 'boolean',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'promo_menu');
    }

    public function foodPackages()
    {
        return $this->belongsToMany(FoodPackage::class, 'promo_food_package');
    }
}
