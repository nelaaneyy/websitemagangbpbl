<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs'; 
    protected $guarded = [];
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public static function record(?string $action, ?string $description = null, ?array $old = null, ?array $new = null): void
    {
        $u = auth()->user();
        static::create([
            'user_id'     => $u?->id,
            'user_name'   => $u?->name,
            'user_role'   => $u?->role,
            'action'      => $action,
            'description' => $description,
            'ip_address'  => request()->ip(),
            'old_values'  => $old,
            'new_values'  => $new,
        ]);
    }
}


