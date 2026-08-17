<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\KepalaDesaController;
use App\Http\Controllers\DinasEsdmController;
use App\Http\Controllers\AuthController;
use App\Models\Desa;

Route::get('/', function () {
    $desas = Desa::orderBy('kabupaten')->orderBy('nama_desa')->get();

    // Hitung otomatis realisasi elektrifikasi desa berbasis status verifikasi pengajuan warga:
    // - Diverifikasi Kades / ESDM (disetujui_desa, lolos_verifikasi_pusat, terpasang) -> Kelompok Teraliri Listrik
    // - Belum Diverifikasi Kades (terkirim, pending) -> Kelompok Belum Teraliri Listrik
    foreach ($desas as $desa) {
        $verifiedCount = \App\Models\Warga::where('desa', 'LIKE', '%' . $desa->nama_desa . '%')
            ->whereIn('status_verifikasi', ['disetujui_desa', 'lolos_verifikasi_pusat', 'terpasang'])
            ->count();

        $pendingCount = \App\Models\Warga::where('desa', 'LIKE', '%' . $desa->nama_desa . '%')
            ->whereIn('status_verifikasi', ['terkirim', 'pending'])
            ->count();

        $totalWargaDesa = $verifiedCount + $pendingCount;

        if ($totalWargaDesa > 0 || $desa->total_rt > 0) {
            $desa->berlistrik_rt = max($desa->berlistrik_rt, $verifiedCount);
            $desa->belum_berlistrik_rt = max(0, $desa->total_rt - $desa->berlistrik_rt) + $pendingCount;
            $desa->total_rt = max($desa->total_rt, $desa->berlistrik_rt + $desa->belum_berlistrik_rt);
            $desa->rasio_elektrifikasi = $desa->total_rt > 0 ? round(($desa->berlistrik_rt / $desa->total_rt) * 100, 1) : 0.0;

            if ($desa->rasio_elektrifikasi >= 100) {
                $desa->status = 'full';
            } elseif ($desa->rasio_elektrifikasi <= 0) {
                $desa->status = 'belum';
            } else {
                $desa->status = 'sebagian';
            }
        }
        
        $desa->warga_terverifikasi = $verifiedCount;
        $desa->warga_pending_kades = $pendingCount;
    }

    $totalTeraliri = $desas->sum('berlistrik_rt');
    $totalRT = $desas->sum('total_rt');
    $overallRasio = $totalRT > 0 ? round(($totalTeraliri / $totalRT) * 100, 2) : 0;
    $totalApprovedGlobal = \App\Models\Warga::whereIn('status_verifikasi', ['disetujui_desa', 'lolos_verifikasi_pusat', 'terpasang'])->count();

    return view('welcome', compact('desas', 'totalTeraliri', 'totalRT', 'overallRasio', 'totalApprovedGlobal'));
})->name('warga.index');

// 2. Halaman Cek Status Berkas (Pencarian NIK) - Rate limited
Route::get('/cek', [WargaController::class, 'search'])->middleware('throttle:20,1')->name('warga.search');

// 3. Halaman Form Input Data Mandiri Warga - Rate limited
Route::get('/input', [WargaController::class, 'create'])->middleware('throttle:15,1')->name('warga.pengajuan');

// 4. Proses Simpan Data & Upload Berkas Foto - Rate limited
Route::post('/input', [WargaController::class, 'store'])->middleware('throttle:10,1')->name('warga.store');

// 5. Unduh Bukti Pendaftaran Resmi PDF (Ber-QR Code)
Route::get('/warga/bukti-pdf/{nik}', [WargaController::class, 'downloadBuktiPdf'])->name('warga.bukti.pdf');

// ==== KEPALA DESA ====
Route::middleware(['auth', 'role:kepala_desa'])->prefix('kepaladesa')->name('kepaladesa.')->group(function () {
    Route::get('/', [KepalaDesaController::class, 'index'])->name('index');

    // Route lisdes (static path HARUS di atas wildcard /{warga})
    Route::get('/lisdes/create', [KepalaDesaController::class, 'createLisdes'])->name('lisdes.create');
    Route::post('/lisdes/store', [KepalaDesaController::class, 'storeLisdes'])->name('lisdes.store');

    Route::get('/{warga}', [KepalaDesaController::class, 'show'])->name('show');
    Route::put('/{warga}', [KepalaDesaController::class, 'update'])->name('update');
    Route::patch('/{warga}/approve', [KepalaDesaController::class, 'approve'])->name('approve');
    Route::patch('/{warga}/reject', [KepalaDesaController::class, 'reject'])->name('reject');
    Route::delete('/{warga}', [KepalaDesaController::class, 'destroy'])->name('destroy');
});

// ==== INSTANSI & VERIFIKATOR ESDM ====
Route::middleware(['auth', 'role:instansi,super_admin,verifikator_esdm'])->prefix('dinasesdm')->name('dinasesdm.')->group(function () {
    Route::get('/', [DinasEsdmController::class, 'index'])->name('index');

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

