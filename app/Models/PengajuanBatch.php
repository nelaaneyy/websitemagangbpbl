<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanBatch extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_batches';

    protected $fillable = [
        'kode_batch',
        'desa',
        'kecamatan',
        'kabupaten',
        'tahun_anggaran',
        'created_by_user_id',
        'kades_id',
        'status',
        'total_warga',
        'nomor_surat_pengantar',
        'sptjm_accepted_at',
        'catatan_kades',
    ];

    protected $casts = [
        'tahun_anggaran' => 'integer',
        'total_warga' => 'integer',
        'sptjm_accepted_at' => 'datetime',
    ];

    public function wargas()
    {
        return $this->hasMany(Warga::class, 'batch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function kades()
    {
        return $this->belongsTo(User::class, 'kades_id');
    }

    public function isFull(): bool
    {
        return $this->wargas()->count() >= 100;
    }

    public function canAddWarga(): bool
    {
        return $this->status === 'draft_staff' && !$this->isFull();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft_staff'        => 'Draft Staff Desa',
            'dikirim_ke_kades'   => 'Menunggu Validasi Kades',
            'diverifikasi_kades' => 'Diverifikasi Kades',
            'diajukan_ke_esdm'   => 'Diajukan ke Dinas ESDM',
            'disetujui_esdm'     => 'Disetujui ESDM (Lolos)',
            'ditolak'            => 'Perlu Perbaikan / Ditolak',
            default              => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
