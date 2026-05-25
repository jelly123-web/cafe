<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const PERMISSIONS = [
        'view_dashboard' => 'Lihat dashboard',
        'view_sales' => 'Lihat penjualan',
        'manage_menus' => 'Kelola menu',
        'manage_branches' => 'Kelola cabang',
        'manage_users' => 'Kelola akun',
        'manage_orders' => 'Manajemen Pesanan',
        'view_all_orders' => 'Melihat semua pesanan',
        'cancel_orders' => 'Membatalkan pesanan',
        'order_history' => 'Riwayat pesanan',
        'monitor_orders_realtime' => 'Monitoring pesanan real-time',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function permissionLabel(string $key): string
    {
        return self::PERMISSIONS[$key] ?? $key;
    }

    public function hasPermission(string $key): bool
    {
        if ($this->role === 'superadmin') {
            return true;
        }

        return (bool) data_get($this->permissions, $key, false);
    }

    public function permissionNames(): array
    {
        return array_keys(self::PERMISSIONS);
    }
}
