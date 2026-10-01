<?php

namespace App\Http\Controllers\StaffDesa;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use App\Models\BerkasWarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $desaUser = auth()->user()->desa;

        $query = Warga::where('desa', $desaUser)
    ->where(function($q) {
        $q->whereNull('batch_id')
          ->orWhereHas('batch', function($b) {
              $b->whereIn('status', ['draft_staff', 'menunggu_kades', 'revisi']);
          })
          ->orWhere('status_verifikasi', 'ditolak/perlu_perbaikan');
    })
    ->whereNotIn('status_verifikasi', [
        'disetujui_desa',
        'menunggu_verifikasi_pusat',
        'lolos_verifikasi_pusat',
        'terpasang',
        'realisasi_selesai',
    ]);

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(fn($q) => $q->where('nama', 'like', "%{$keyword}%")
                                    ->orWhere('nik', 'like', "%{$keyword}%"));
        }
    return view('staffdesa.index', ['wargas' => $query->latest()->paginate(10)]);
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
        $warga = $nik ? Warga::where('nik', $nik)->where('status_verifikasi', 'ditolak/perlu_perbaikan')->first() : null;

        $desa   = auth()->user()->desa;
        $tahun  = (int) date('Y');

        $activeBatch = \App\Models\PengajuanBatch::firstOrCreate(
        ['desa' => $desa, 'tahun_anggaran' => $tahun],
        [
            'kode_batch'         => 'BPBL-' . $tahun . '-' . \Illuminate\Support\Str::slug($desa) . '-' . strtoupper(\Illuminate\Support\Str::random(4)),
            'kecamatan'          => auth()->user()->kecamatan ?? null,
            'kabupaten'          => auth()->user()->kabupaten ?? null,
            'created_by_user_id' => auth()->id(),
            'status'             => 'draft_staff',
        ]
    );
    $activeBatch->total_warga = $activeBatch->wargas()->count();
    $activeBatch->save();

    return view('staffdesa.pengajuan', compact('nik', 'warga', 'activeBatch'));
}

    public function edit(Warga $warga)
{
    return view('staffdesa.edit', compact('warga'));
}

public function storeDraft(Request $request)
{
    $validated = $request->validate([
        'nik'       => 'required|numeric|digits:16',
        'nama'      => 'required|string|max:255',
        'kabupaten' => 'required|string|max:255',
        'kecamatan' => 'required|string|max:255',
        'desa'      => 'required|string|max:255',
        'rt_rw'     => 'required|string|max:10',
        'no_hp'     => 'required|numeric',
        'alamat'    => 'required|string',
        'latitude'  => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
    ]);

    $desa  = auth()->user()->desa;
    $tahun = (int) date('Y');

    $activeBatch = \App\Models\PengajuanBatch::firstOrCreate(
        ['desa' => $desa, 'tahun_anggaran' => $tahun],
        [
            'kode_batch'         => 'BPBL-' . $tahun . '-' . \Illuminate\Support\Str::slug($desa) . '-' . strtoupper(\Illuminate\Support\Str::random(4)),
            'kecamatan'          => auth()->user()->kecamatan ?? null,
            'kabupaten'          => auth()->user()->kabupaten ?? null,
            'created_by_user_id' => auth()->id(),
            'status'             => 'draft_staff',
        ]
    );

    Warga::create(array_merge($validated, [
        'batch_id'          => $activeBatch->id,
        'status_verifikasi' => 'draft',
        'tahun_usulan'      => $tahun,
        'created_by_user_id' => auth()->id(),
    ]));

    $activeBatch->update(['total_warga' => $activeBatch->wargas()->count()]);

    return redirect()->route('staffdesa.index')->with('success', 'Draft berhasil disimpan.');
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
                'desil'                 => $request->input('desil'),
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

        return redirect()->route('staffdesa.pengajuan',)
            ->with('success', 'Warga'. $validated['nama'] . ' berhasil didaftarkan. Silakan tunggu proses verifikasi dari Kepala Desa.');
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

    public function update(Request $request, Warga $warga)
{
    $validated = $request->validate([
        'nik'       => 'required|numeric|digits:16',
        'nama'      => 'required|string|max:255',
        'kabupaten' => 'required|string|max:255',
        'kecamatan' => 'required|string|max:255',
        'desa'      => 'required|string|max:255',
        'rt_rw'     => 'required|string|max:10',
        'no_hp'     => 'required|numeric',
        'alamat'    => 'required|string',
        'latitude'  => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'foto_ktp'                  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'foto_rumah_depan'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'foto_kwh_rumah_terdekat'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'foto_tiang_rumah_terdekat' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'foto_sktm'                 => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $warga->update($validated);

    // Update foto hanya kalau ada yang diupload
    if ($warga->berkas) {
        $fotoFields = [
            'foto_ktp', 'foto_rumah_depan',
            'foto_kwh_rumah_terdekat', 'foto_tiang_rumah_terdekat', 'foto_sktm'
        ];
        $berkasData = [];
        foreach ($fotoFields as $field) {
            if ($request->hasFile($field)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($warga->berkas->$field);
                $berkasData[$field] = $request->file($field)->store('berkas/' . $field, 'public');
            }
        }
        if (!empty($berkasData)) {
            $warga->berkas->update($berkasData);
        }
    }

    \App\Models\ActivityLog::record(
        'warga_edit',
        'Staff desa mengedit data warga NIK ' . $warga->nik . ' (' . $warga->nama . ')'
    );

    return redirect()->route('staffdesa.index')->with('success', 'Data warga berhasil diperbarui.');
}
}
