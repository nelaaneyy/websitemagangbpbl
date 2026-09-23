<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $table = 'work_orders';

    protected $fillable = [
        'warga_id',
        'vendor_id',
        'petugas_id',
        'nomor_wo',
        'tanggal_wo',
        'status',
        'foto_pemasangan',
        'foto_kwh_terpasang',
        'nomor_kwh_meter',
        'tanggal_pasang',
        'catatan_vendor',
        'catatan_verifikator',
    ];

    protected $casts = [
        'tanggal_wo' => 'date',
        'tanggal_pasang' => 'datetime',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
