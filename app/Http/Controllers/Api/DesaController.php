<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DesaController extends Controller
{
    public function index()
    {
        $desas = DB::table('desas')
            ->select([
                'id',
                'nama_desa',
                'kabupaten',
                'latitude',
                'longitude',
                'status',
                'rasio_elektrifikasi',
                'total_rt',
                'berlistrik_rt',
                'belum_berlistrik_rt'
            ])
            ->whereNotNull('latitude')
            ->where('latitude', '!=', 0)
            ->get();

        // Hitung statistik real-time dari tabel wargas
        $wargaStats = DB::table('wargas')
            ->selectRaw('LOWER(TRIM(desa)) as clean_desa, 
                COUNT(*) as total_rt, 
                SUM(CASE WHEN status_verifikasi = "terpasang" THEN 1 ELSE 0 END) as berlistrik_rt,
                SUM(CASE WHEN status_verifikasi IN ("terkirim", "pending", "menunggu_verifikasi_pusat", "disetujui_desa") THEN 1 ELSE 0 END) as pending_kades')
            ->whereNotNull('desa')
            ->where('desa', '!=', '')
            ->groupBy(DB::raw('LOWER(TRIM(desa))'))
            ->get()
            ->keyBy('clean_desa');

        $desas->transform(function ($desa) use ($wargaStats) {
            $cleanName = mb_strtolower(trim($desa->nama_desa));
            if (isset($wargaStats[$cleanName])) {
                $stat = $wargaStats[$cleanName];
                $total = (int)$stat->total_rt;
                $berlistrik = (int)$stat->berlistrik_rt;
                $belum = max(0, $total - $berlistrik);
                $rasio = $total > 0 ? round(($berlistrik / $total) * 100, 1) : (float)$desa->rasio_elektrifikasi;
                
                $status = 'sebagian';
                if ($rasio >= 100 && $berlistrik > 0) {
                    $status = 'full';
                } elseif ($rasio <= 0) {
                    $status = 'belum';
                }

                $desa->total_rt = $total;
                $desa->berlistrik_rt = $berlistrik;
                $desa->belum_berlistrik_rt = $belum;
                $desa->rasio_elektrifikasi = $rasio;
                $desa->status = $status;
                $desa->warga_terverifikasi = $berlistrik;
                $desa->warga_pending_kades = (int)$stat->pending_kades;
            } else {
                $desa->total_rt = (int)$desa->total_rt;
                $desa->berlistrik_rt = (int)$desa->berlistrik_rt;
                $desa->belum_berlistrik_rt = (int)$desa->belum_berlistrik_rt;
                $desa->rasio_elektrifikasi = (float)$desa->rasio_elektrifikasi;
                $desa->warga_terverifikasi = (int)$desa->berlistrik_rt;
                $desa->warga_pending_kades = 0;
            }
            return $desa;
        });

        return response()->json([
            'status' => 'success',
            'total'  => $desas->count(),
            'data'   => $desas
        ]);
    }
}

