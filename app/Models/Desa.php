<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_desa',
        'kabupaten',
        'latitude',
        'longitude',
        'total_rt',
        'berlistrik_rt',
        'belum_berlistrik_rt',
        'rasio_elektrifikasi',
        'status',
    ];

    protected static function booted()
    {
        static::saving(function ($desa) {
            // Hitung otomatis belum_berlistrik_rt
            $total = max(0, (int)$desa->total_rt);
            $berlistrik = max(0, (int)$desa->berlistrik_rt);

            if ($berlistrik > $total) {
                $berlistrik = $total;
            }

            $desa->total_rt = $total;
            $desa->berlistrik_rt = $berlistrik;
            $desa->belum_berlistrik_rt = $total - $berlistrik;

            // Hitung rasio elektrifikasi (0.0 - 100.0)
            if ($total > 0) {
                $desa->rasio_elektrifikasi = round(($berlistrik / $total) * 100, 1);
            } else {
                $desa->rasio_elektrifikasi = 0.0;
            }

            // Tentukan status marker (full, sebagian, belum)
            if ($desa->rasio_elektrifikasi >= 100) {
                $desa->status = 'full';
            } elseif ($desa->rasio_elektrifikasi <= 0) {
                $desa->status = 'belum';
            } else {
                $desa->status = 'sebagian';
            }
        });
    }

    // Helper warna marker leaflet
    public function getColorAttribute()
    {
        if ($this->status === 'full') {
            return 'green';
        } elseif ($this->status === 'belum') {
            return 'red';
        }
        return 'gold';
    }
}
