<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nipd',
        'email',
        'password',
        'role',
        'desa',
        'kabupaten',
        'kecamatan',
        'no_hp',
        'sk_file',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'vendor_id');
    }

    // ---- ROLE CHECKERS ----
    public function hasAnyRole(...$roles): bool
    {
        if (empty($roles)) {
            return false;
        }

        if (is_array($roles[0])) {
            $roles = $roles[0];
        }

        return in_array($this->role, $roles, true);
    }


    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    // Staff Administrasi Desa
    public function isStaffDesa(): bool
    {
        return $this->role === 'staff_desa';
    }

    // Kepala Desa
    public function isKepalaDesa(): bool
    {
        return $this->role === 'kepala_desa';
    }

    // Verifikator ESDM
    public function isVerifikatorEsdm(): bool
    {
        return $this->role === 'verifikator_esdm';
    }

    /** ACT-05: Admin ESDM (Super Admin / Manajemen) */
    public function isAdminEsdm(): bool
    {
        return in_array($this->role, ['super_admin', 'instansi', 'admin_esdm']);
    }

    /** Backward Compatibility Checker */
    public function isSuperAdmin(): bool
    {
        return $this->isAdminEsdm();
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'super_admin', 'admin', 'instansi', 'admin_esdm' => 'Super Admin ESDM',
            'verifikator_esdm', 'verifikator' => 'Verifikator ESDM',
            'kepala_desa' => 'Kepala Desa',
            'staff_desa' => 'Staff Administrasi Desa',
            'warga', 'publik' => 'Warga / Pendamping',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }
}
