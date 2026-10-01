<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaffDesa\PengajuanController;
use App\Http\Controllers\KepalaDesa\KepalaDesaController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Verifikator\VerifikatorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchController;
use App\Models\Desa;

Route::get('/', function () {
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
        $namaDesa  = mb_convert_case($first->desa, MB_CASE_TITLE, 'UTF-8');
        $kabupaten = mb_convert_case($first->kabupaten ?: 'Kota Jambi', MB_CASE_TITLE, 'UTF-8');
        $kecamatan = mb_convert_case($first->kecamatan ?: 'Alam Barajo', MB_CASE_TITLE, 'UTF-8');

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
            $lat = $wargaWithCoords ? (float)$wargaWithCoords->latitude : -1.6300;
            $lng = $wargaWithCoords ? (float)$wargaWithCoords->longitude : 103.5680;
        }

        $pengajuanDesa = \App\Models\Warga::whereRaw('LOWER(desa) = ?', [$cleanName])->count();
        $realisasiDesa = \App\Models\Warga::whereRaw('LOWER(desa) = ?', [$cleanName])
            ->where('status_verifikasi', 'terpasang')
            ->count();
        $pendingDesa = \App\Models\Warga::whereRaw('LOWER(desa) = ?', [$cleanName])
            ->whereIn('status_verifikasi', ['terkirim', 'pending', 'menunggu_verifikasi_pusat', 'disetujui_desa'])
            ->count();
        $rasio = $pengajuanDesa > 0 ? round(($realisasiDesa / $pengajuanDesa) * 100, 1) : 0.0;

        $desas->push((object)[
            'id'                  => $dbDesa ? $dbDesa->id : 9000,
            'nama_desa'           => $namaDesa,
            'kabupaten'           => $kabupaten,
            'kecamatan'           => $kecamatan,
            'latitude'            => $lat,
            'longitude'           => $lng,
            'total_rt'            => $pengajuanDesa,
            'berlistrik_rt'       => $realisasiDesa,
            'belum_berlistrik_rt' => max(0, $pengajuanDesa - $realisasiDesa),
            'rasio_elektrifikasi' => $rasio,
            'status'              => $rasio >= 100 && $realisasiDesa > 0 ? 'full' : ($rasio <= 0 ? 'belum' : 'sebagian'),
            'warga_terverifikasi' => $realisasiDesa,
            'warga_pending_kades' => $pendingDesa,
        ]);
    }
    if ($desas->isEmpty()) {
        $dbDesas = Desa::orderBy('kabupaten')->orderBy('nama_desa')->get();
        foreach ($dbDesas as $desa) {
            $realisasiDesa              = \App\Models\Warga::where('desa', 'LIKE', '%' . $desa->nama_desa . '%')->where('status_verifikasi', 'terpasang')->count();
            $pengajuanDesa              = \App\Models\Warga::where('desa', 'LIKE', '%' . $desa->nama_desa . '%')->count();
            $pendingDesa                = \App\Models\Warga::where('desa', 'LIKE', '%' . $desa->nama_desa . '%')->whereIn('status_verifikasi', ['terkirim', 'pending', 'menunggu_verifikasi_pusat', 'disetujui_desa'])->count();
            $desa->berlistrik_rt        = $realisasiDesa;
            $desa->belum_berlistrik_rt  = max(0, $pengajuanDesa - $realisasiDesa);
            $desa->total_rt             = $pengajuanDesa;
            $desa->rasio_elektrifikasi  = $pengajuanDesa > 0 ? round(($realisasiDesa / $pengajuanDesa) * 100, 1) : 0.0;
            $desa->status               = $desa->rasio_elektrifikasi >= 100 ? 'full' : ($desa->rasio_elektrifikasi <= 0 ? 'belum' : 'sebagian');
            $desas->push($desa);
        }
    }

    $totalWarga             = \App\Models\Warga::count();
    $totalTerpasang         = \App\Models\Warga::whereIn('status_verifikasi', ['terpasang', 'lolos_verifikasi_pusat'])->count();
    $overallRasio           = $totalWarga > 0 ? round(($totalTerpasang / $totalWarga) * 100, 2) : 0.0;
    $totalApprovedGlobal    = \App\Models\Warga::whereIn('status_verifikasi', ['disetujui_desa', 'lolos_verifikasi_pusat', 'terpasang'])->count();

    return view('welcome', compact('desas', 'totalTerpasang', 'totalWarga', 'overallRasio', 'totalApprovedGlobal'));
})->name('warga.index');

// API PUBLIK
Route::get('/api/kpi-stats', function () {
    $totalWarga             = \App\Models\Warga::count();
    $totalTerpasang         = \App\Models\Warga::whereIn('status_verifikasi', ['terpasang', 'lolos_verifikasi_pusat'])->count();
    $totalMenunggu          = \App\Models\Warga::whereIn('status_verifikasi', ['menunggu_verifikasi_pusat', 'disetujui_desa', 'terkirim', 'pending'])->count();

    return response()->json([
        'total_teraliri'        => $totalTerpasang,
        'total_rt'              => $totalWarga,
        'overall_rasio'         => $totalWarga > 0 ? round(($totalTerpasang / $totalWarga) * 100, 2) : 0,
        'total_approved_global' => \App\Models\Warga::whereIn('status_verifikasi', ['disetujui_desa', 'lolos_verifikasi_pusat', 'terpasang'])->count(),
        'total_warga'           => $totalWarga,
        'total_menunggu'        => $totalMenunggu,
        'total_disetujui'       => \App\Models\Warga::whereIn('status_verifikasi', ['lolos_verifikasi_pusat', 'terpasang'])->count(),
        'total_ditolak'         => \App\Models\Warga::where('status_verifikasi', 'ditolak/perlu_perbaikan')->count(),
        'total_terpasang'       => $totalTerpasang,
    ]);
})->name('api.kpi.stats');

// WILAYAH API
Route::get('/api/wilayah/districts/{kabId}', function ($kabId) {
    return \Illuminate\Support\Facades\Cache::remember("districts_{$kabId}", 86400 * 30, function () use ($kabId) {
        try { return \Illuminate\Support\Facades\Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/districts/{$kabId}.json")->json() ?? []; }
        catch (\Exception $e) { return []; }
    });
});

Route::get('/api/wilayah/villages/{kecId}', function ($kecId) {
    return \Illuminate\Support\Facades\Cache::remember("villages_{$kecId}", 86400 * 30, function () use ($kecId) {
        try { return \Illuminate\Support\Facades\Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/villages/{$kecId}.json")->json() ?? []; }
        catch (\Exception $e) { return []; }
    });
});

// === AUTH ===
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/register-desa', [AuthController::class, 'showRegisterDesa'])->name('register.desa');
Route::post('/register-desa', [AuthController::class, 'registerDesa'])->name('register.desa.submit');

// === PANDUAN ====
Route::get('/panduan', fn() => view('panduan'))->name('panduan.index');
Route::get('/panduan-alias', fn() => redirect()->route('panduan.index'))->name('panduan');
Route::get('/panduan/buku-saku-pdf', fn() => view('buku_saku_pdf'))->name('panduan.pdf');

// ==== KEPALA DESA ====
Route::middleware(['auth', 'role:kepala_desa'])->prefix('kepaladesa')->name('kepaladesa.')->group(function () {
    Route::get('/', [KepalaDesaController::class, 'index'])->name('index');
    Route::get('/lisdes', [KepalaDesaController::class, 'lisdesIndex'])->name('lisdes.index');
    Route::get('/lisdes/create', [KepalaDesaController::class, 'createLisdes'])->name('lisdes.create');
    Route::get('/lisdes/{lisdes}/edit', [KepalaDesaController::class, 'editLisdes'])->name('lisdes.edit');
    Route::put('/lisdes/{lisdes}', [KepalaDesaController::class, 'updateLisdes'])->name('lisdes.update');
    Route::post('/lisdes/store', [KepalaDesaController::class, 'storeLisdes'])->name('lisdes.store');
    Route::get('/batch/{id}/review', [BatchController::class, 'reviewForm'])->name('batch.review');
    Route::post('/batch/{id}/approve-and-send', [BatchController::class, 'approveAndSendToEsdm'])->name('batch.approve');
    Route::post('/batch/{batch}/submit-esdm', [KepalaDesaController::class, 'submitBatchToEsdm'])->name('batch.submit_esdm');
    // Wildcard
    Route::get('/{warga}', [KepalaDesaController::class, 'show'])->name('show');
    Route::put('/{warga}', [KepalaDesaController::class, 'update'])->name('update');
    Route::patch('/{warga}/approve', [KepalaDesaController::class, 'approve'])->name('approve');
    Route::patch('/{warga}/reject', [KepalaDesaController::class, 'reject'])->name('reject');
    Route::delete('/{warga}', [KepalaDesaController::class, 'destroy'])->name('destroy');
});

// ==== STAFF DESA ====
Route::middleware(['auth', 'role:staff_desa,staf_desa'])->prefix('staffdesa')->name('staffdesa.')->group(function () {
    Route::get('/', [PengajuanController::class, 'index'])->name('index');
    Route::get('/pengajuan', [PengajuanController::class, 'create'])->name('pengajuan');
    Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('store');
    Route::get('/cek', [PengajuanController::class, 'search'])->name('cek');
    Route::get('/warga/{warga}/edit', [PengajuanController::class, 'edit'])->name('warga.edit');
    Route::put('/warga/{warga}', [PengajuanController::class, 'update'])->name('warga.update');
    Route::get('/warga/bukti-pdf/{nik}', [PengajuanController::class, 'downloadBuktiPdf'])->name('warga.bukti.pdf');
    Route::post('/warga/store-draft', [PengajuanController::class, 'storeDraft'])->name('warga.store.draft');
    Route::post('/batch/create-from-draft', [BatchController::class, 'createFromDraft'])->name('batch.create');
    Route::post('/batch/{batch}/kirim-ke-kades', [BatchController::class, 'kirimBatchKeKades'])->name('batch.kirim');
});

// ==== VERIFIKATOR ESDM ====
Route::middleware(['auth', 'role:verifikator_esdm'])->prefix('verifikator')->name('verifikator.')->group(function () {
    Route::get('/', [VerifikatorController::class, 'index'])->name('index');
    Route::get('/datalist', [VerifikatorController::class, 'dataList'])->name('datalist');

    Route::get('/redundancy/{warga}', [\App\Http\Controllers\RedundancyResolutionController::class, 'show'])->name('redundancy.show');
    Route::patch('/redundancy/{warga}/resolve', [\App\Http\Controllers\RedundancyResolutionController::class, 'resolve'])->name('redundancy.resolve');
    Route::get('/users/manage', [VerifikatorController::class, 'users'])->name('users.index');
    Route::patch('/users/manage/{user}/approve', [VerifikatorController::class, 'approveUser'])->name('users.approve');
    Route::patch('/users/manage/{user}/reject', [VerifikatorController::class, 'rejectUser'])->name('users.reject');
    Route::get('/warga/{warga}/bavl-pdf', [VerifikatorController::class, 'downloadBavlPdf'])->name('warga.bavl.pdf');
    Route::get('/lisdes/{id}/map-pdf', [VerifikatorController::class, 'downloadLisdesMapPdf'])->name('lisdes.map.pdf');
    Route::patch('/{warga}/catatan', [VerifikatorController::class, 'addCatatan'])->name('catatan');

    // Validasi realisasi (reuse AdminController)
    Route::get('/validasi-realisasi', [AdminController::class, 'validasiRealisasiIndex'])->name('validasi_realisasi.index');
    Route::patch('/validasi-realisasi/{warga}/confirm', [AdminController::class, 'konfirmasiRealisasi'])->name('validasi_realisasi.confirm');
    Route::post('/validasi-realisasi/bulk-confirm', [AdminController::class, 'bulkKonfirmasiRealisasi'])->name('validasi_realisasi.bulk_confirm');

    // Lisdes (read-only & approve/reject)
    Route::get('/lisdes', [AdminController::class, 'lisdesIndex'])->name('lisdes.index');
    Route::get('/lisdes/{lisdes}', [AdminController::class, 'lisdesShow'])->name('lisdes.show');
    Route::patch('/lisdes/{lisdes}/approve', [AdminController::class, 'lisdesApprove'])->name('lisdes.approve');
    Route::patch('/lisdes/{lisdes}/reject', [AdminController::class, 'lisdesReject'])->name('lisdes.reject');

    // Historis
    Route::get('/historis', [AdminController::class, 'historisIndex'])->name('historis.index');

    Route::get('/export/excel', [VerifikatorController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/pdf', [VerifikatorController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/export/kml', [AdminController::class, 'exportKml'])->name('export.kml');
    Route::get('/backup', [AdminController::class, 'backupDatabase'])->name('backup');

    Route::get('/import/template', [AdminController::class, 'downloadImportTemplate'])->name('import.template');
    Route::post('/import', [AdminController::class, 'importExcel'])->name('import.excel');

    // Wildcard (harus paling bawah)
    Route::get('/{warga}', [VerifikatorController::class, 'show'])->name('show');
    Route::patch('/{warga}/approve', [VerifikatorController::class, 'approve'])->name('approve');
    Route::patch('/{warga}/reject', [VerifikatorController::class, 'reject'])->name('reject');
    Route::delete('/{warga}', [VerifikatorController::class, 'destroy'])->name('destroy');
});

// ==== SUPER ADMIN ====
Route::middleware(['auth', 'role:instansi,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/datalist', [AdminController::class, 'dataList'])->name('datalist');
    Route::get('/audit-logs',[AdminController::class, 'auditLogs'])->name('audit_logs.index');

    // export import
    Route::get('/export/excel', [AdminController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/pdf', [AdminController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/export/kml', [AdminController::class, 'exportKml'])->name('export.kml');
    Route::get('/import/template', [AdminController::class, 'downloadImportTemplate'])->name('import.template');
    Route::post('/import', [AdminController::class, 'importExcel'])->name('import.excel');

    // legalitas
    Route::patch('/warga/{warga}/legalitas', [AdminController::class, 'updateLegalitas'])->name('warga.legalitas');

    // work orders
    Route::get('/work-orders', [\App\Http\Controllers\WorkOrderController::class, 'index'])->name('work_orders.index');
    Route::post('/work-orders', [\App\Http\Controllers\WorkOrderController::class, 'store'])->name('work_orders.store');
    Route::match(['put', 'patch'], '/work-orders/{workOrder}/status', [\App\Http\Controllers\WorkOrderController::class, 'updateStatus'])->name('work_orders.updateStatus');

    // lisdes
    Route::get('/lisdes', [AdminController::class, 'lisdesIndex'])->name('lisdes.index');
    Route::get('/lisdes/{lisdes}', [AdminController::class, 'lisdesShow'])->name('lisdes.show');
    Route::patch('/lisdes/{lisdes}/approve', [AdminController::class, 'lisdesApprove'])->name('lisdes.approve');
    Route::patch('/lisdes/{lisdes}/reject', [AdminController::class, 'lisdesReject'])->name('lisdes.reject');
    Route::delete('/lisdes/{lisdes}', [AdminController::class, 'lisdesDestroy'])->name('lisdes.destroy');
    Route::get('/lisdes/{id}/map-pdf', [AdminController::class, 'downloadLisdesMapPdf'])->name('lisdes.map.pdf');
    Route::post('/lisdes/clusters/generate', [AdminController::class, 'generateLisdesClusters'])->name('lisdes.clusters.generate');

    // desa
    Route::get('/desa', [AdminController::class, 'desaIndex'])->name('desa.index');
    Route::get('/desa/create', [AdminController::class, 'createDesa'])->name('desa.create');
    Route::post('/desa', [AdminController::class, 'storeDesa'])->name('desa.store');
    Route::get('/desa/{desa}/edit', [AdminController::class, 'editDesa'])->name('desa.edit');
    Route::put('/desa/{desa}', [AdminController::class, 'updateDesa'])->name('desa.update');
    Route::delete('/desa/{desa}', [AdminController::class, 'destroyDesa'])->name('desa.destroy');

    // electric pole
    Route::post('/electric-poles/import', [AdminController::class, 'importElectricPoles'])->name('poles.import');
    Route::get('/electric-poles/geojson', [AdminController::class, 'exportElectricPolesGeoJson'])->name('poles.geojson');

    // validasi realisasi
    Route::get('/validasi-realisasi', [AdminController::class, 'validasiRealisasiIndex'])->name('validasi_realisasi.index');
    Route::patch('/validasi-realisasi/{warga}/confirm', [AdminController::class, 'konfirmasiRealisasi'])->name('validasi_realisasi.confirm');
    Route::post('/validasi-realisasi/bulk-confirm', [AdminController::class, 'bulkKonfirmasiRealisasi'])->name('validasi_realisasi.bulk_confirm');

    // historis
    Route::get('/historis', [AdminController::class, 'historisIndex'])->name('historis.index');

    // user management
    Route::get('/users/manage', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/manage/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users/manage', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/manage/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/manage/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/manage/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::patch('/users/manage/{user}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
    Route::patch('/users/manage/{user}/reject', [AdminController::class, 'rejectUser'])->name('users.reject');

    // backup
    Route::get('/backup', [AdminController::class, 'backupDatabase'])->name('backup');

    // Wildcard (harus paling bawah)
    Route::get('/{warga}', [AdminController::class, 'show'])->name('show');
    Route::patch('/{warga}/approve', [AdminController::class, 'approve'])->name('approve');
    Route::patch('/{warga}/reject', [AdminController::class, 'reject'])->name('reject');
    Route::delete('/{warga}', [AdminController::class, 'destroy'])->name('destroy');
});

