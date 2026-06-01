<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiningTable extends Model
{
    use HasFactory;

    public const STATUS_EMPTY = 'empty';
    public const STATUS_OCCUPIED = 'occupied';

    protected $table = 'tables';

    protected $fillable = [
        'number',
        'name',
        'qr_token',
        'is_active',
        'service_status',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function serviceStatusLabel(): string
    {
        return $this->service_status === self::STATUS_OCCUPIED ? 'Terisi' : 'Kosong';
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
