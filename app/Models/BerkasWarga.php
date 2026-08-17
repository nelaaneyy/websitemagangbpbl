<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BerkasWarga extends Model
{
    use HasFactory;

    protected $fillable = [
        'warga_id',
        'foto_ktp',
        'foto_rumah_depan',
        'foto_kwh_rumah_terdekat',
        'foto_tiang_rumah_terdekat',
        'foto_sktm',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }
}
