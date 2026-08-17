<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanLisdes extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_lisdes';

    protected $fillable = [
        'user_id',
        'desa',
        'nama_dusun',
        'jumlah_kk',
        'estimasi_jarak',
        'keterangan_wilayah',
        'surat_permohonan',
        'proposal_lisdes',
        'foto_wilayah',
        'latitude',
        'longitude',
        'status',
        'catatan_esdm',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
