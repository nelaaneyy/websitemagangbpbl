<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'description',
        'ip_address',
    ];

    public function user()
    {
        return $table = $this->belongsTo(User::class);
    }

    /**
     * Helper method to log activities cleanly across controllers
     */
    public static function record($action, $description = null, $user = null)
    {
        $currentUser = $user ?? auth()->user();
        
        return self::create([
            'user_id'     => $currentUser ? $currentUser->id : null,
            'user_name'   => $currentUser ? $currentUser->name : 'Warga (Publik)',
            'user_role'   => $currentUser ? $currentUser->role : 'publik',
            'action'      => $action,
            'description' => $description,
            'ip_address'  => request()->ip(),
        ]);
    }
}
