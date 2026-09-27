<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\BerkasWarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->check() && in_array(auth()->user()->role, ['staff_desa', 'staf_desa'])) {
            $desaUser = auth()->user()->desa;

            // Query dasar: Ambil warga sesuai desa staff
            $query = Warga::where('desa', $desaUser);

            $query->where(function($q) {
                $q->whereNull('batch_id')
                ->orWhere('status_verifikasi', 'Perlu Perbaikan');
            });

            // PASTIKAN membuang/mengecualikan status yang sudah lolos/realisasi/proses lanjutan
            $query->whereNotIn('status_verifikasi', [
                'Disetujui',
                'Terpasang',
                'Selesai',
                'Proses Verifikasi',
                'Dikirim ke Kades'
            ]);

            // Fitur Pencarian NIK / Nama jika ada
            if ($request->filled('q')) {
                $keyword = $request->q;
                $query->where(function($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('nik', 'like', "%{$keyword}%");
                });
            }

            $wargas = $query->latest()->paginate(10);

            return view('staffdesa.index', compact('wargas'));
        }
    // Tampilan default untuk umum/warga publik
    return view('welcome');
}

    public function search(Request $request)
    {
        $nik = $request->input('nik');
        $warga = null;
        $notFound = false;

        if ($nik) {
            $request->validate([
                'nik' => 'numeric|digits:16',
            ], [
                'nik.digits' => 'NIK harus terdiri dari 16 digit angka.',
            ]);

            $warga = Warga::with('berkas')->where('nik', $nik)->first();

            if (!$warga) {
                $notFound = true;
            }
        }

        return view('staffdesa.search', compact('warga', 'nik', 'notFound'));
    }

    public function create(Request $request)
    {
        $nik = $request->query('nik');
        $warga = null;
        if ($nik) {
            $warga = Warga::where('nik', $nik)->where('status_verifikasi', 'ditolak/perlu_perbaikan')->first();
        }

        $activeBatch = null;
        if (auth()->check() && (auth()->user()->isStaffDesa() || auth()->user()->isKepalaDesa())) {
            $desa = auth()->user()->desa;
            $tahun = (int)date('Y');
            $activeBatch = \App\Models\PengajuanBatch::firstOrCreate(
                [
                    'desa' => $desa,
                    'tahun_anggaran' => $tahun,
                ],
                [
                    'kode_batch' => 'BPBL-' . $tahun . '-' . \Illuminate\Support\Str::slug($desa) . '-' . strtoupper(\Illuminate\Support\Str::random(4)),
                    'kecamatan' => auth()->user()->kecamatan ?? null,
                    'kabupaten' => auth()->user()->kabupaten ?? null,
                    'created_by_user_id' => auth()->id(),
                    'status' => 'draft_staff',
                ]
            );
            $activeBatch->total_warga = $activeBatch->wargas()->count();
            $activeBatch->save();

            if (request()->routeIs('staffdesa.*') || auth()->user()->isStaffDesa()) {
                return view('staffdesa.pengajuan', compact('nik', 'warga', 'activeBatch'));
            }
        }

        return view('warga.pengajuan', compact('nik', 'warga', 'activeBatch'));
    }

    public function store(Request $request)
    {
        $wargaExists = Warga::where('nik', $request->nik)->first();
        $isResubmit = false;

        if ($wargaExists) {
            if ($wargaExists->status_verifikasi !== 'ditolak/perlu_perbaikan') {
                return back()->withErrors(['nik' => 'NIK ini sudah terdaftar dalam sistem dan tidak dalam masa perbaikan.'])->withInput();
            }
            $isResubmit = true;
        }

        $validated = $request->validate([
            'nik'                         => 'required|numeric|digits:16',
            'nama'                        => 'required|string|max:255',
            'kabupaten'                   => 'required|string|max:255',
            'kecamatan'                   => 'required|string|max:255',
            'desa'                        => 'required|string|max:255',
            'rt_rw'                       => 'required|string|max:10',
            'desil'                       => 'nullable|string|max:50',
            'no_hp'                       => 'required|numeric',
            'alamat'                      => 'required|string',
            'latitude'                    => 'required|numeric|between:-90,90',
            'longitude'                   => 'required|numeric|between:-180,180',
            'foto_ktp'                    => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_rumah_depan'            => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_kwh_rumah_terdekat'     => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_tiang_rumah_terdekat'   => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_sktm'                   => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'persetujuan'                 => 'accepted',
        ],
        [
            'nik.required'                         => 'NIK wajib diisi.',
            'nik.digits'                           => 'NIK harus berjumlah 16 digit.',
            'latitude.required'                    => 'Titik lokasi GPS (Latitude) wajib dideteksi.',
            'longitude.required'                   => 'Titik lokasi GPS (Longitude) wajib dideteksi.',
            'foto_ktp.required'                    => 'Foto KTP wajib di-upload.',
            'foto_rumah_depan.required'            => 'Foto rumah tampak depan wajib diupload menggunakan aplikasi GPS Camera.',
            'foto_kwh_rumah_terdekat.required'     => 'Foto KWH rumah terdekat wajib diupload menggunakan aplikasi GPS Camera.',
            'foto_tiang_rumah_terdekat.required'   => 'Foto tiang listrik terdekat wajib diupload menggunakan aplikasi GPS Camera.',
            'foto_sktm.required'                   => 'Foto SKTM wajib di-upload.',
            'persetujuan.accepted'                 => 'Anda harus menyetujui pernyataan kebenaran data untuk melanjutkan.',
            '*.max'                                => 'Ukuran foto maksimal adalah 2 MB.',
        ]);

        // Pengelolaan Batching Desa (Maksimal 100 data warga per batch per tahun)
        $desa = $validated['desa'];
        $tahun = (int)date('Y');
        $activeBatch = \App\Models\PengajuanBatch::firstOrCreate(
            [
                'desa' => $desa,
                'tahun_anggaran' => $tahun,
            ],
            [
                'kode_batch' => 'BPBL-' . $tahun . '-' . \Illuminate\Support\Str::slug($desa) . '-' . strtoupper(\Illuminate\Support\Str::random(4)),
                'kecamatan' => $validated['kecamatan'] ?? null,
                'kabupaten' => $validated['kabupaten'] ?? null,
                'created_by_user_id' => auth()->id() ?? null,
                'status' => 'draft_staff',
            ]
        );

        if (!$isResubmit && $activeBatch->isFull()) {
            return back()->withErrors(['error' => 'Kuota pengajuan usulan desa tahun ' . $tahun . ' (' . $desa . ') sudah mencapai batas maksimal 100 data warga!'])->withInput();
        }

        // ---- 1. Eksekusi 3-Layer Duplicate Checking Engine ----
        $dupChecker = new \App\Services\DuplicateCheckingService();
        $dupResult = $dupChecker->check(
            $validated['nik'],
            (float)$validated['latitude'],
            (float)$validated['longitude'],
            $request->input('id_pelanggan'),
            $validated['no_hp'],
            $isResubmit ? $wargaExists->id : null
        );

        // ---- 2. Eksekusi EXIF Validation Engine pada Foto Rumah ----
        $exifService = new \App\Services\ExifValidationService();
        $exifResult = $exifService->validatePhotoExif(
            $request->file('foto_rumah_depan'),
            (float)$validated['latitude'],
            (float)$validated['longitude']
        );

        // ---- 3. Hitung Jarak Tiang Spasial Presisi (Haversine) ----
        $nearestPoleInfo = \App\Services\SpatialEngine::findNearestPole((float)$validated['latitude'], (float)$validated['longitude']);
        $calculatedDistance = $nearestPoleInfo ? $nearestPoleInfo['distance_meters'] : 0.0;

        DB::transaction(function () use ($request, $validated, $isResubmit, $wargaExists, $dupResult, $exifResult, $calculatedDistance, $activeBatch, $tahun) {
            $wargaData = [
                'batch_id'              => $activeBatch->id,
                'nik'                   => $validated['nik'],
                'no_kk'                 => $request->input('no_kk'),
                'id_pelanggan'          => $request->input('id_pelanggan'),
                'nama'                  => $validated['nama'],
                'kabupaten'             => $validated['kabupaten'],
                'kecamatan'             => $validated['kecamatan'],
                'desa'                  => $validated['desa'],
                'rt_rw'                 => $validated['rt_rw'],
                'desil'                 => $request->input('desil', 'Desil 1'),
                'no_hp'                 => $validated['no_hp'],
                'alamat'                => $validated['alamat'],
                'latitude'              => $validated['latitude'],
                'longitude'             => $validated['longitude'],
                'exif_latitude'         => $exifResult['exif_latitude'],
                'exif_longitude'        => $exifResult['exif_longitude'],
                'exif_device'           => $exifResult['exif_device'],
                'exif_timestamp'        => $exifResult['exif_timestamp'],
                'is_exif_valid'         => $exifResult['is_valid'],
                'exif_deviation_meters' => $exifResult['deviation_meters'],
                'jarak_tiang_calc'      => $calculatedDistance,
                'risiko_duplikasi'      => $dupResult['risk_level'],
                'catatan_duplikasi'     => $dupResult['summary'],
                'status_verifikasi'     => 'terkirim',
                'tahun_usulan'          => $tahun,
                'created_by_user_id'    => auth()->id() ?? null,
                'catatan'               => null,
                'ditolak_oleh'          => null,
            ];

            if ($isResubmit) {
                $wargaExists->update($wargaData);
                $warga = $wargaExists;

                // Hapus berkas lama
                if ($warga->berkas) {
                    $filesToDelete = [
                        $warga->berkas->foto_ktp,
                        $warga->berkas->foto_rumah_depan,
                        $warga->berkas->foto_kwh_rumah_terdekat,
                        $warga->berkas->foto_tiang_rumah_terdekat,
                    ];
                    if ($warga->berkas->foto_sktm) {
                        $filesToDelete[] = $warga->berkas->foto_sktm;
                    }
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($filesToDelete);
                    $warga->berkas->delete();
                }
            } else {
                $warga = Warga::create($wargaData);
            }

            $pathKtp   = $request->file('foto_ktp')->store('berkas/ktp', 'public');
            $pathDepan = $request->file('foto_rumah_depan')->store('berkas/rumah', 'public');
            $pathKwh   = $request->file('foto_kwh_rumah_terdekat')->store('berkas/rumah', 'public');
            $pathTiang = $request->file('foto_tiang_rumah_terdekat')->store('berkas/rumah', 'public');
            $pathSktm  = $request->file('foto_sktm')->store('berkas/sktm', 'public');

            BerkasWarga::create([
                'warga_id'                    => $warga->id,
                'foto_ktp'                    => $pathKtp,
                'foto_rumah_depan'            => $pathDepan,
                'foto_kwh_rumah_terdekat'     => $pathKwh,
                'foto_tiang_rumah_terdekat'   => $pathTiang,
                'foto_sktm'                   => $pathSktm,
            ]);

            // Update kuota batch
            $activeBatch->update([
                'total_warga' => $activeBatch->wargas()->count(),
            ]);

            // Record Immutable Activity Log with GPS Coords & State Snapshot
            \App\Models\ActivityLog::record(
                $isResubmit ? 'warga_resubmit' : 'warga_register',
                ($isResubmit ? 'Perbaikan data pendaftaran BPBL untuk NIK ' : 'Pendaftaran baru BPBL untuk NIK ') . $warga->nik . ' (' . $warga->nama . ' - ' . $warga->desa . ', ' . $warga->kabupaten . ')',
                null,
                "{$warga->latitude},{$warga->longitude}",
                ['nik' => $warga->nik, 'risk' => $dupResult['risk_level'], 'exif_valid' => $exifResult['is_valid']]
            );
        });

        if (auth()->check() && auth()->user()->isStaffDesa()) {
            return redirect()->route('staffdesa.pengajuan')
                ->with('success', 'Warga ' . $validated['nama'] . ' berhasil ditambahkan ke dalam Batch Pengajuan ' . $activeBatch->kode_batch . ' (Total: ' . $activeBatch->total_warga . '/100 warga).');
        }

        $message = $isResubmit ? 'Perbaikan berkas pendaftaran berhasil dikirim!' : 'Pendaftaran bantuan listrik berhasil dikirim! Silakan cek status berkas Anda secara berkala.';

        return redirect()->route('warga.index', ['nik' => $request->nik])
            ->with('success', $message);
    }

    /**
     * Unduh Bukti Pendaftaran Resmi PDF (Ber-QR Code)
     */
    public function downloadBuktiPdf($nik)
    {
        $warga = Warga::where('nik', $nik)->firstOrFail();

        $filename = 'Bukti_Pendaftaran_BPBL_' . $warga->nik . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('warga.bukti_pdf', compact('warga'))
            ->setPaper('a4', 'portrait');

        // Record log download
        \App\Models\ActivityLog::record(
            'warga_download_pdf',
            'Mengunduh bukti pendaftaran PDF resmi untuk NIK ' . $warga->nik
        );

        return $pdf->download($filename);
    }


    public function processData()
    {
        set_time_limit(300);

        Warga::chunk(100, function ($wargas) {
            foreach ($wargas as $warga) {
            }
        });

        return redirect()->back()->with('success', 'Data berhasil diproses!');
    }
}
