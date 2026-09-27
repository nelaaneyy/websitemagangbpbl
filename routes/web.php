<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\KepalaDesaController;
use App\Http\Controllers\DinasEsdmController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchController;
use App\Models\Desa;

Route::get('/', function () {
    // 1. Kumpulkan seluruh desa REAL yang memiliki data pengajuan/realisasi dari tabel Warga
    $wargaDesas = \App\Models\Warga::select('desa', 'kecamatan', 'kabupaten')
        ->whereNotNull('desa')
        ->where('desa', '!=', '')
        ->get()
        ->groupBy(function($item) {
            return mb_strtolower(trim($item->desa));
        });

    $desas = collect();

    foreach ($wargaDesas as $cleanName => $items) {
        $first = $items->first();
        $namaDesa = strtoupper($first->desa);
        $kabupaten = $first->kabupaten ?: 'KOTA JAMBI';
        $kecamatan = $first->kecamatan ?: 'ALAM BARAJO';

        $dbDesa = Desa::whereRaw('LOWER(nama_desa) = ?', [$cleanName])->first();

        $knownCoords = [
            'bagan pete'       => ['lat' => -1.6435, 'lng' => 103.5580],
            'kenali besar'     => ['lat' => -1.6244345, 'lng' => 103.5501182],
            'mayang mangurai'  => ['lat' => -1.6220, 'lng' => 103.5840],
            'rawasari'         => ['lat' => -1.6160, 'lng' => 103.5750],
            'beliung'          => ['lat' => -1.6250, 'lng' => 103.5910],
            'telanaipura'      => ['lat' => -1.6008, 'lng' => 103.5872],
            'mendalo darat'    => ['lat' => -1.6042, 'lng' => 103.5298],
            'muara bulian'     => ['lat' => -1.7265, 'lng' => 103.2652],
        ];

        if ($dbDesa && (float)$dbDesa->latitude != 0 && (float)$dbDesa->longitude != 0) {
            $lat = (float)$dbDesa->latitude;
            $lng = (float)$dbDesa->longitude;
        } elseif (isset($knownCoords[$cleanName])) {
            $lat = $knownCoords[$cleanName]['lat'];
            $lng = $knownCoords[$cleanName]['lng'];
        } else {
            $wargaWithCoords = \App\Models\Warga::whereRaw('LOWER(desa) = ?', [$cleanName])
                ->whereNotNull('latitude')
                ->where('latitude', '!=', 0)
                ->first();
            if ($wargaWithCoords) {
                $lat = (float)$wargaWithCoords->latitude;
                $lng = (float)$wargaWithCoords->longitude;
            } elseif (strtoupper($kecamatan) === 'ALAM BARAJO') {
                $lat = -1.6300 + (rand(-10, 10) / 1000.0);
                $lng = 103.5680 + (rand(-10, 10) / 1000.0);
            } else {
                $lat = -1.6000 + (rand(-20, 20) / 1000.0);
                $lng = 103.5800 + (rand(-20, 20) / 1000.0);
            }
        }

        $pengajuanDesa = \App\Models\Warga::whereRaw('LOWER(desa) = ?', [$cleanName])->count();
        $realisasiDesa = \App\Models\Warga::whereRaw('LOWER(desa) = ?', [$cleanName])
            ->where('status_verifikasi', 'terpasang')
            ->count();
        $pendingDesa = \App\Models\Warga::whereRaw('LOWER(desa) = ?', [$cleanName])
            ->whereIn('status_verifikasi', ['terkirim', 'pending', 'menunggu_verifikasi_pusat', 'disetujui_desa'])
            ->count();

        $rasio = $pengajuanDesa > 0 ? round(($realisasiDesa / $pengajuanDesa) * 100, 1) : 0.0;

        $status = 'sebagian';
        if ($rasio >= 100 && $realisasiDesa > 0) {
            $status = 'full';
        } elseif ($rasio <= 0) {
            $status = 'belum';
        }

        $desas->push((object)[
            'id'                  => $dbDesa ? $dbDesa->id : (9000 + rand(1, 999)),
            'nama_desa'           => $namaDesa,
            'kabupaten'           => $kabupaten,
            'kecamatan'           => $kecamatan,
            'latitude'            => $lat,
            'longitude'           => $lng,
            'total_rt'            => $pengajuanDesa,
            'berlistrik_rt'       => $realisasiDesa,
            'belum_berlistrik_rt' => max(0, $pengajuanDesa - $realisasiDesa),
            'rasio_elektrifikasi' => $rasio,
            'status'              => $status,
            'warga_terverifikasi' => $realisasiDesa,
            'warga_pending_kades' => $pendingDesa,
        ]);
    }

    // Jika belum ada warga, fallback ke Desa yang diinput manual
    if ($desas->isEmpty()) {
        $dbDesas = Desa::orderBy('kabupaten')->orderBy('nama_desa')->get();
        foreach ($dbDesas as $desa) {
            $realisasiDesa = \App\Models\Warga::where('desa', 'LIKE', '%' . $desa->nama_desa . '%')
                ->where('status_verifikasi', 'terpasang')
                ->count();
            $pengajuanDesa = \App\Models\Warga::where('desa', 'LIKE', '%' . $desa->nama_desa . '%')->count();
            $pendingDesa = \App\Models\Warga::where('desa', 'LIKE', '%' . $desa->nama_desa . '%')
                ->whereIn('status_verifikasi', ['terkirim', 'pending', 'menunggu_verifikasi_pusat', 'disetujui_desa'])
                ->count();

            $desa->berlistrik_rt = $realisasiDesa;
            $desa->belum_berlistrik_rt = max(0, $pengajuanDesa - $realisasiDesa);
            $desa->total_rt = $pengajuanDesa;
            $desa->rasio_elektrifikasi = $pengajuanDesa > 0 ? round(($realisasiDesa / $pengajuanDesa) * 100, 1) : 0.0;
            $desa->status = $desa->rasio_elektrifikasi >= 100 ? 'full' : ($desa->rasio_elektrifikasi <= 0 ? 'belum' : 'sebagian');
            $desas->push($desa);
        }
    }

    $totalWarga = \App\Models\Warga::count();
    $totalTerpasang = \App\Models\Warga::whereIn('status_verifikasi', ['terpasang', 'lolos_verifikasi_pusat'])->count();

    $totalTeraliri = $totalTerpasang;
    $totalRT = $totalWarga;
    $overallRasio = $totalWarga > 0 ? round(($totalTerpasang / $totalWarga) * 100, 2) : 0.0;
    $totalApprovedGlobal = \App\Models\Warga::whereIn('status_verifikasi', ['disetujui_desa', 'lolos_verifikasi_pusat', 'terpasang'])->count();

    return view('welcome', compact('desas', 'totalTeraliri', 'totalRT', 'overallRasio', 'totalApprovedGlobal'));
})->name('warga.index');

Route::get('/api/kpi-stats', function () {
    $totalWarga = \App\Models\Warga::count();
    $totalTerpasang = \App\Models\Warga::whereIn('status_verifikasi', ['terpasang', 'lolos_verifikasi_pusat'])->count();
    $overallRasio = $totalWarga > 0 ? round(($totalTerpasang / $totalWarga) * 100, 2) : 0;

    $totalMenunggu = \App\Models\Warga::whereIn('status_verifikasi', ['menunggu_verifikasi_pusat', 'disetujui_desa', 'terkirim', 'pending'])->count();
    $totalDisetujui = \App\Models\Warga::whereIn('status_verifikasi', ['lolos_verifikasi_pusat', 'terpasang'])->count();
    $totalDitolak = \App\Models\Warga::where('status_verifikasi', 'ditolak/perlu_perbaikan')->count();
    $totalApprovedGlobal = \App\Models\Warga::whereIn('status_verifikasi', ['disetujui_desa', 'lolos_verifikasi_pusat', 'terpasang'])->count();

    return response()->json([
        'total_teraliri' => $totalTerpasang,
        'total_rt' => $totalWarga,
        'overall_rasio' => $overallRasio,
        'total_approved_global' => $totalApprovedGlobal,
        'total_warga' => $totalWarga,
        'total_menunggu' => $totalMenunggu,
        'total_disetujui' => $totalDisetujui,
        'total_ditolak' => $totalDitolak,
        'total_terpasang' => $totalTerpasang,
    ]);
})->name('api.kpi.stats');

// 2. Fitur Input & Cek Status Pendaftaran BPBL (Khusus Perangkat Desa & ESDM)
Route::middleware(['auth', 'role:staff_desa,kepala_desa,verifikator_esdm,super_admin,instansi'])->group(function () {
    Route::get('/cek', [WargaController::class, 'search'])->name('staffdesa.cek');
    Route::get('/input', [WargaController::class, 'create'])->name('warga.pengajuan');
    Route::post('/input', [WargaController::class, 'store'])->name('warga.store');
    Route::get('/warga/bukti-pdf/{nik}', [WargaController::class, 'downloadBuktiPdf'])->name('warga.bukti.pdf');
});

// ==== KEPALA DESA ====
Route::middleware(['auth', 'role:kepala_desa,kades'])->prefix('kepaladesa')->name('kepaladesa.')->group(function () {
    Route::get('/', [KepalaDesaController::class, 'index'])->name('index');

    // 1. Static & Batch Routes (HARUS ditaruh SEBELUM wildcard /{warga})
    Route::get('/lisdes', [KepalaDesaController::class, 'lisdesIndex'])->name('lisdes.index');
    Route::get('/lisdes/create', [KepalaDesaController::class, 'createLisdes'])->name('lisdes.create');
    Route::post('/lisdes/store', [KepalaDesaController::class, 'storeLisdes'])->name('lisdes.store');

    // Route Khusus Kades: Review & Kirim Batch + S&K ke ESDM
    Route::get('/batch/{id}/review', [BatchController::class, 'reviewForm'])->name('batch.review');
    Route::post('/batch/{id}/approve-and-send', [BatchController::class, 'approveAndSendToEsdm'])->name('batch.approve');
    Route::post('/batch/{batch}/submit-esdm', [KepalaDesaController::class, 'submitBatchToEsdm'])->name('batch.submit_esdm');

    // 2. Wildcard Routes (Selalu ditaruh paling bawah di dalam grup)
    Route::get('/{warga}', [KepalaDesaController::class, 'show'])->name('show');
    Route::put('/{warga}', [KepalaDesaController::class, 'update'])->name('update');
    Route::patch('/{warga}/approve', [KepalaDesaController::class, 'approve'])->name('approve');
    Route::patch('/{warga}/reject', [KepalaDesaController::class, 'reject'])->name('reject');
    Route::delete('/{warga}', [KepalaDesaController::class, 'destroy'])->name('destroy');
});


// ==== STAFF ADMINISTRASI DESA (ACT-02) ====
Route::middleware(['auth', 'role:staf_desa,staff_desa,kepala_desa,kades'])->prefix('staffdesa')->name('staffdesa.')->group(function () {
    Route::get('/', [WargaController::class, 'index'])->name('index');
    Route::get('/pengajuan', [WargaController::class, 'create'])->name('pengajuan');
    Route::get('/cek', [WargaController::class, 'search'])->name('cek');

    // Tambahkan route edit warga ini
    Route::get('/warga/{warga}/edit', [WargaController::class, 'edit'])->name('warga.edit');
    Route::put('/warga/{warga}', [WargaController::class, 'update'])->name('warga.update');

    // Aksi Input & Batch Draf oleh Staff
    Route::post('/warga/store-draft', [WargaController::class, 'storeDraft'])->name('warga.store.draft');
    Route::post('/batch/create-from-draft', [BatchController::class, 'createFromDraft'])->name('batch.create');
    Route::post('/batch/{batch}/kirim-ke-kades', [BatchController::class, 'kirimBatchKeKades'])->name('batch.kirim');
});

// ==== INSTANSI, VERIFIKATOR ESDM, PETUGAS LAPANGAN & VENDOR PLN ====
Route::middleware(['auth', 'role:instansi,super_admin,verifikator_esdm,petugas_lapangan,pln_vendor'])->prefix('dinasesdm')->name('dinasesdm.')->group(function () {
    Route::get('/', [DinasEsdmController::class, 'index'])->name('index');
    Route::get('/datalist', [DinasEsdmController::class, 'dataList'])->name('datalist');

    // Audit Trail Immutable Logs
    Route::get('/audit-logs', [DinasEsdmController::class, 'auditLogs'])->name('audit_logs.index');

    // Redundancy Resolution Side-by-Side Panel (UC-ESDM-RED-01)
    Route::get('/redundancy/{warga}', [\App\Http\Controllers\RedundancyResolutionController::class, 'show'])->name('redundancy.show');
    Route::patch('/redundancy/{warga}/resolve', [\App\Http\Controllers\RedundancyResolutionController::class, 'resolve'])->name('redundancy.resolve');

    // Legalitas NIDI / SLO / BAST (Admin ESDM ACT-05)
    Route::patch('/warga/{warga}/legalitas', [DinasEsdmController::class, 'updateLegalitas'])->name('warga.legalitas');

    // Work Orders Management (PLN Vendor & Field Officer & ESDM)
    Route::get('/work-orders', [\App\Http\Controllers\WorkOrderController::class, 'index'])->name('work_orders.index');
    Route::post('/work-orders', [\App\Http\Controllers\WorkOrderController::class, 'store'])->name('work_orders.store');
    Route::match(['put', 'patch'], '/work-orders/{workOrder}/status', [\App\Http\Controllers\WorkOrderController::class, 'updateStatus'])->name('work_orders.updateStatus');

    // Export & Document Generators
    Route::get('/export/kml', [DinasEsdmController::class, 'exportKml'])->name('export.kml');
    Route::get('/warga/{warga}/bavl-pdf', [DinasEsdmController::class, 'downloadBavlPdf'])->name('warga.bavl.pdf');
    Route::get('/lisdes/{id}/map-pdf', [DinasEsdmController::class, 'downloadLisdesMapPdf'])->name('lisdes.map.pdf');

    // Electric Poles Spatial Ingestion
    Route::post('/electric-poles/import', [DinasEsdmController::class, 'importElectricPoles'])->name('poles.import');
    Route::get('/electric-poles/geojson', [DinasEsdmController::class, 'exportElectricPolesGeoJson'])->name('poles.geojson');

    // Spatial Clustering Engine (DBSCAN / K-Means)
    Route::post('/lisdes/clusters/generate', [DinasEsdmController::class, 'generateLisdesClusters'])->name('lisdes.clusters.generate');

    // Pengajuan Lisdes oleh Kepala Desa (kelola oleh ESDM) - HARUS di atas /{warga}
    Route::get('/lisdes', [DinasEsdmController::class, 'lisdesIndex'])->name('lisdes.index');
    Route::get('/lisdes/{lisdes}', [DinasEsdmController::class, 'lisdesShow'])->name('lisdes.show');
    Route::patch('/lisdes/{lisdes}/approve', [DinasEsdmController::class, 'lisdesApprove'])->name('lisdes.approve');
    Route::patch('/lisdes/{lisdes}/reject', [DinasEsdmController::class, 'lisdesReject'])->name('lisdes.reject');
    Route::delete('/lisdes/{lisdes}', [DinasEsdmController::class, 'lisdesDestroy'])->name('lisdes.destroy');

    // Kelola data desa / rasio elektrifikasi (peta geospasial)
    Route::get('/desa', [DinasEsdmController::class, 'desaIndex'])->name('desa.index');
    Route::get('/desa/create', [DinasEsdmController::class, 'createDesa'])->name('desa.create');
    Route::post('/desa', [DinasEsdmController::class, 'storeDesa'])->name('desa.store');
    Route::get('/desa/{desa}/edit', [DinasEsdmController::class, 'editDesa'])->name('desa.edit');
    Route::put('/desa/{desa}', [DinasEsdmController::class, 'updateDesa'])->name('desa.update');
    Route::delete('/desa/{desa}', [DinasEsdmController::class, 'destroyDesa'])->name('desa.destroy');

    // Fitur Ekspor & Import Data Pengajuan BPBL (Excel & PDF Resmi) - HARUS di atas /{warga}
    Route::get('/export/excel', [DinasEsdmController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/pdf', [DinasEsdmController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/import/template', [DinasEsdmController::class, 'downloadImportTemplate'])->name('import.template');
    Route::post('/import', [DinasEsdmController::class, 'importExcel'])->name('import.excel');

    // Validasi Realisasi Data Lama SuperAdmin (HARUS di atas /{warga})
    Route::get('/validasi-realisasi', [DinasEsdmController::class, 'validasiRealisasiIndex'])->name('validasi_realisasi.index');
    Route::patch('/validasi-realisasi/{warga}/confirm', [DinasEsdmController::class, 'konfirmasiRealisasi'])->name('validasi_realisasi.confirm');
    Route::post('/validasi-realisasi/bulk-confirm', [DinasEsdmController::class, 'bulkKonfirmasiRealisasi'])->name('validasi_realisasi.bulk_confirm');

    // Fitur Arsip & Data Historis Realisasi BPBL (HARUS di atas /{warga})
    Route::get('/historis', [DinasEsdmController::class, 'historisIndex'])->name('historis.index');

    // Role management - Verifikasi Akun Desa (Bisa diakses Verifikator ESDM & Super Admin)
    Route::get('/users/manage', [DinasEsdmController::class, 'users'])->name('users.index');
    Route::patch('/users/manage/{user}/approve', [DinasEsdmController::class, 'approveUser'])->name('users.approve');
    Route::patch('/users/manage/{user}/reject', [DinasEsdmController::class, 'rejectUser'])->name('users.reject');

    // ==== KHUSUS SUPER ADMIN ESDM (Tambah, Edit, Hapus Akun & Backup) ====
    Route::middleware(['role:instansi,super_admin'])->group(function () {
        Route::get('/users/manage/create', [DinasEsdmController::class, 'createUser'])->name('users.create');
        Route::post('/users/manage', [DinasEsdmController::class, 'storeUser'])->name('users.store');
        Route::get('/users/manage/{user}/edit', [DinasEsdmController::class, 'editUser'])->name('users.edit');
        Route::put('/users/manage/{user}', [DinasEsdmController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/manage/{user}', [DinasEsdmController::class, 'destroyUser'])->name('users.destroy');

        Route::get('/backup', [DinasEsdmController::class, 'backupDatabase'])->name('backup');
    });

    // Detail warga (wildcard route terakhir)
    Route::get('/{warga}', [DinasEsdmController::class, 'show'])->name('show');
    Route::patch('/{warga}/approve', [DinasEsdmController::class, 'approve'])->name('approve');
    Route::patch('/{warga}/reject', [DinasEsdmController::class, 'reject'])->name('reject');
    Route::delete('/{warga}', [DinasEsdmController::class, 'destroy'])->name('destroy');
});

// ==== PANDUAN PELATIHAN PERANGKAT DESA ====
Route::get('/panduan', function () {
    return view('panduan');
})->name('panduan.index');
Route::get('/panduan-alias', function () {
    return redirect()->route('panduan.index');
})->name('panduan');

Route::get('/panduan/buku-saku-pdf', function () {
    return view('buku_saku_pdf');
})->name('panduan.pdf');

// ==== AUTH ====

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register-desa', [AuthController::class, 'showRegisterDesa'])->name('register.desa');
Route::post('/register-desa', [AuthController::class, 'registerDesa'])->name('register.desa.submit');

// ==== API INTEGRASI DTKS KEMENSOS & WILAYAH ====
Route::get('/api/dtks/check/{nik}', [WargaController::class, 'checkDtksApi'])->name('api.dtks.check');

Route::get('/api/wilayah/districts/{kabId}', function ($kabId) {
    return \Illuminate\Support\Facades\Cache::remember("districts_{$kabId}", 86400 * 30, function () use ($kabId) {
        try {
            $response = \Illuminate\Support\Facades\Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/districts/{$kabId}.json");
            return $response->json() ?? [];
        } catch (\Exception $e) {
            return [];
        }
    });
});

Route::get('/api/wilayah/villages/{kecId}', function ($kecId) {
    return \Illuminate\Support\Facades\Cache::remember("villages_{$kecId}", 86400 * 30, function () use ($kecId) {
        try {
            $response = \Illuminate\Support\Facades\Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/villages/{$kecId}.json");
            return $response->json() ?? [];
        } catch (\Exception $e) {
            return [];
        }
    });
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

