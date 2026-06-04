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

    public const ROLE_LABELS = [
        'superadmin' => 'Superadmin',
        'admin' => 'Admin',
        'kasir' => 'Kasir',
        'staff' => 'Kasir',
        'leader_cashier' => 'Leader Kasir',
        'kitchen' => 'Dapur',
        'inventory' => 'Gudang',
    ];

    public const PERMISSIONS = [
        'superadmin_dashboard' => 'Superadmin: Dashboard',
        'superadmin_users' => 'Superadmin: Akun Pengguna',
        'superadmin_access' => 'Superadmin: Hak Akses',
        'superadmin_menus' => 'Superadmin: Manajemen Menu',
        'superadmin_employees' => 'Superadmin: Data Karyawan',
        'superadmin_payrolls' => 'Superadmin: Gaji Karyawan',
        'superadmin_menu_categories' => 'Superadmin: Kategori Menu',
        'superadmin_tables' => 'Superadmin: Meja',
        'superadmin_reports' => 'Superadmin: Laporan',
        'superadmin_settings' => 'Superadmin: Pengaturan Sistem',

        'cashier_orders' => 'Kasir: Pesanan',
        'cashier_transactions' => 'Kasir: Transaksi',
        'cashier_payments' => 'Kasir: Pembayaran',
        'cashier_receipts' => 'Kasir: Struk',
        'cashier_tables' => 'Kasir: Meja',
        'cashier_reports' => 'Kasir: Laporan Kasir',

        'kitchen_orders' => 'Dapur: Pesanan Masuk',
        'kitchen_history' => 'Dapur: Riwayat Pesanan',
        'kitchen_menus' => 'Dapur: Menu Habis',

        'inventory_index' => 'Gudang: Inventory',
        'inventory_movement' => 'Gudang: Barang Masuk/Keluar',

        'leader_monitoring' => 'Leader Kasir: Monitoring',
        'leader_cashflow' => 'Leader Kasir: Kas Masuk/Keluar',
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
        'profile_photo_path',
        'google_id',
        'phone_number',
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

    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
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

    public function roleLabel(): string
    {
        $role = match (strtolower(trim((string) $this->role))) {
            'dapur' => 'kitchen',
            default => strtolower(trim((string) $this->role)),
        };

        return self::ROLE_LABELS[$role] ?? $role;
    }
}
