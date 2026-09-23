<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LisdesCluster extends Model
{
    use HasFactory;

    protected $table = 'lisdes_clusters';

    protected $fillable = [
        'kode_cluster',
        'nama_cluster',
        'desa',
        'kecamatan',
        'kabupaten',
        'jumlah_usulan',
        'total_panjang_jaringan_meter',
        'centroid_lat',
        'centroid_lng',
        'status',
        'proposal_data',
        'estimasi_anggaran',
    ];

    protected $casts = [
        'proposal_data' => 'array',
    ];
}
