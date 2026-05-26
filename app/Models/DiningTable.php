<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiningTable extends Model
{
    use HasFactory;

    protected $table = 'tables';

    protected $fillable = [
        'number',
        'name',
        'qr_token',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(SaleTransaction::class, 'table_id');
    }

    public function getRouteKeyName(): string
    {
        return 'qr_token';
    }
}
