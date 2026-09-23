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
        'gps_coords',
        'state_snapshot',
    ];

    protected $casts = [
        'state_snapshot' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper method to log immutable audit activities cleanly across controllers
     */
    public static function record($action, $description = null, $user = null, $gpsCoords = null, $stateSnapshot = null)
    {
        $currentUser = $user ?? auth()->user();
        
        return self::create([
            'user_id'        => $currentUser ? $currentUser->id : null,
            'user_name'      => $currentUser ? $currentUser->name : 'Warga (Publik)',
            'user_role'      => $currentUser ? $currentUser->role : 'publik',
            'action'         => $action,
            'description'    => $description,
            'ip_address'     => request()->ip(),
            'gps_coords'     => $gpsCoords ?? request()->header('X-GPS-Coords'),
            'state_snapshot' => $stateSnapshot,
        ]);
    }
}
