<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BerkasWarga;
use App\Helpers\DataPrivacyHelper;

class Warga extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'nik',
        'no_kk',
        'id_pelanggan',
        'nama',
        'kabupaten',
        'kecamatan',
        'desa',
        'dusun',
        'rt_rw',
        'desil',
        'no_hp',
        'alamat',
        'jarak_tiang',
        'latitude',
        'longitude',
        'exif_latitude',
        'exif_longitude',
        'exif_device',
        'exif_timestamp',
        'is_exif_valid',
        'exif_deviation_meters',
        'jarak_tiang_calc',
        'risiko_duplikasi',
        'catatan_duplikasi',
        'redundancy_flag',
        'foto_sekat_fisik',
        'redundancy_resolution_note',
        'status_verifikasi',
        'butuh_validasi_realisasi',
        'tahun_usulan',
        'keterangan_import',
        'catatan',
        'ditolak_oleh',
        'no_nidi',
        'no_slo',
        'file_bast',
        'surat_pengantar_desa',
        'created_by_user_id',
    ];

    protected $casts = [
        'exif_timestamp' => 'datetime',
        'is_exif_valid' => 'boolean',
        'butuh_validasi_realisasi' => 'boolean',
    ];

    public function batch()
    {
        return $this->belongsTo(PengajuanBatch::class, 'batch_id');
    }

    public function berkas()
    {
        return $this->hasOne(BerkasWarga::class);
    }

    public function workOrder()
    {
        return $this->hasOne(WorkOrder::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Accessor NIK Masked (3515************)
     */
    public function getNikMaskedAttribute()
    {
        return DataPrivacyHelper::maskNik($this->nik);
    }

    /**
     * Accessor Nomor KK Masked
     */
    public function getKkMaskedAttribute()
    {
        return DataPrivacyHelper::maskKk($this->no_kk);
    }

    /**
     * Scope filter data warga yang membutuhkan validasi realisasi SuperAdmin
     */
    public function scopeButuhValidasiRealisasi($query)
    {
        return $query->where('butuh_validasi_realisasi', true);
    }

    public function scopeHistoris($q)
    {
    return $q->where(function ($w) {
        $w->where('status_verifikasi', 'terpasang')
          ->orWhereNotNull('keterangan_import');
    });
}

public function scopeAktif($q)
{
    return $q->whereNull('keterangan_import')
             ->where('status_verifikasi', '!=', 'terpasang');
}

public function scopeUntukAdmin($q)
{
    return $q->aktif()->whereIn('status_verifikasi', ['lolos_verifikasi_pusat', 'terpasang', 'ditolak/perlu_perbaikan']);
}

}
