<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BerkasWarga;

class Warga extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'nama',
        'kabupaten',
        'kecamatan',
        'desa',
        'dusun',
        'rt_rw',
        'no_hp',
        'alamat',
        'jarak_tiang',
        'latitude',
        'longitude',
        'status_verifikasi',
        'catatan',
    ];

    public function berkas()
    {
        return $this->hasOne(BerkasWarga::class);
    }

    

}
