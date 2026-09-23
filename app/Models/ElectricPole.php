<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricPole extends Model
{
    use HasFactory;

    protected $table = 'electric_poles';

    protected $fillable = [
        'kode_tiang',
        'jenis',
        'latitude',
        'longitude',
        'kapasitas_kva',
        'status',
        'kondisi',
        'alamat',
        'desa',
        'kecamatan',
        'kabupaten',
    ];

    /**
     * Scope filter tiang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
