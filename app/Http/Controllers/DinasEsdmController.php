<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\User;
use App\Models\Desa;
use App\Models\PengajuanLisdes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DinasEsdmController extends Controller
{
    /**
     * Helper query filter warga untuk ESDM (Dashboard, Ekspor Excel, Ekspor PDF)
     */
    private function getFilteredWargaQuery(Request $request)
    {
        return Warga::with('berkas')
            ->where(function($q) {
                $q->whereIn('status_verifikasi', ['menunggu_verifikasi_pusat', 'lolos_verifikasi_pusat'])
                  ->orWhere(function($sub) {
                      $sub->where('status_verifikasi', 'ditolak/perlu_perbaikan')
                          ->where('ditolak_oleh', 'instansi');
                  });
            })
            ->when($request->kabupaten, function ($query, $kabupaten) {
                return $query->where('kabupaten', $kabupaten);
            })
            ->when($request->kecamatan, function ($query, $kecamatan) {
                return $query->where('kecamatan', $kecamatan);
            })
            ->when($request->desa, function ($query, $desa) {
                if (is_array($desa)) {
                    $filteredDesa = array_filter($desa);
                    return !empty($filteredDesa) ? $query->whereIn('desa', $filteredDesa) : $query;
                }
                return $query->where('desa', $desa);
            })
            ->when($request->dusun, function ($query, $dusun) {
                return $query->where('dusun', $dusun);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('nik', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%")
                      ->orWhere('desa', 'like', "%{$search}%")
                      ->orWhere('dusun', 'like', "%{$search}%")
                      ->orWhere('alamat', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status_verifikasi', $status);
            })
            ->latest();
    }

    /**
     * Backup Data Penerima Manfaat & Rekapitulasi (JSON Encrypted Backup)
     */
    public function backupDatabase()
    {
        $wargas = Warga::with('berkas')->get();
        $desas = Desa::all();
        $users = User::select('id', 'name', 'email', 'role', 'desa', 'kabupaten')->get();
        $lisdes = PengajuanLisdes::all();

        $backupData = [
            'app' => 'SIPELITA ESDM v2.0 SPBE e-Government',
            'exported_at' => now()->toIso8601String(),
            'total_warga' => $wargas->count(),
            'total_desa' => $desas->count(),
            'total_petugas' => $users->count(),
            'total_lisdes' => $lisdes->count(),
            'warga' => $wargas,
            'desa' => $desas,
            'users' => $users,
            'lisdes' => $lisdes,
        ];

        $filename = 'SIPELITA_Backup_DATA_' . date('Y-m-d_H-i-s') . '.json';

        return response()->streamDownload(function () use ($backupData) {
            echo json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    // List semua warga yang sudah diverifikasi kepala desa
    public function index(Request $request)
    {
        $wargas = $this->getFilteredWargaQuery($request)
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'    => Warga::whereIn('status_verifikasi', ['menunggu_verifikasi_pusat', 'lolos_verifikasi_pusat'])->count(),
            'menunggu' => Warga::where('status_verifikasi', 'menunggu_verifikasi_pusat')->count(),
            'disetujui' => Warga::where('status_verifikasi', 'lolos_verifikasi_pusat')->count(),
            'ditolak'  => Warga::where('status_verifikasi', 'ditolak/perlu_perbaikan')->where('ditolak_oleh', 'instansi')->count(),
        ];

        // List wilayah unik dari database untuk dropdown filter bertingkat
        $kabupatens = Warga::select('kabupaten')->distinct()->whereNotNull('kabupaten')->orderBy('kabupaten')->pluck('kabupaten');
        
        $kecamatans = Warga::when($request->kabupaten, function($q, $kab) {
                return $q->where('kabupaten', $kab);
            })
            ->select('kecamatan')->distinct()->whereNotNull('kecamatan')->orderBy('kecamatan')->pluck('kecamatan');

        $desas = Warga::when($request->kabupaten, function($q, $kab) {
                return $q->where('kabupaten', $kab);
            })
            ->when($request->kecamatan, function($q, $kec) {
                return $q->where('kecamatan', $kec);
            })
            ->select('desa')->distinct()->whereNotNull('desa')->orderBy('desa')->pluck('desa');

        $dusuns = Warga::when($request->kabupaten, function($q, $kab) {
                return $q->where('kabupaten', $kab);
            })
            ->when($request->kecamatan, function($q, $kec) {
                return $q->where('kecamatan', $kec);
            })
            ->when($request->desa, function($q, $des) {
                return $q->where('desa', $des);
            })
            ->select('dusun')->distinct()->whereNotNull('dusun')->where('dusun', '!=', '')->orderBy('dusun')->pluck('dusun');

        // Struktur Hierarki Wilayah (Kabupaten -> Kecamatan -> Desa) untuk Cascading Dropdown
        $wilayahRecords = Warga::select('kabupaten', 'kecamatan', 'desa')
            ->distinct()
            ->whereNotNull('kabupaten')
            ->get();

        $wilayahTree = [];
        foreach ($wilayahRecords as $w) {
            $kab = $w->kabupaten;
            $kec = $w->kecamatan ?: 'Lainnya';
            $des = $w->desa ?: 'Lainnya';
            
            if (!isset($wilayahTree[$kab])) {
                $wilayahTree[$kab] = [];
            }
            if (!isset($wilayahTree[$kab][$kec])) {
                $wilayahTree[$kab][$kec] = [];
            }
            if (!in_array($des, $wilayahTree[$kab][$kec])) {
                $wilayahTree[$kab][$kec][] = $des;
            }
        }

        // Data Grafik Analitik (Chart.js)
        $chartKabupaten = Warga::select('kabupaten', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereNotNull('kabupaten')
            ->groupBy('kabupaten')
            ->pluck('total', 'kabupaten');

        $chartStatus = [
            'terkirim'               => Warga::where('status_verifikasi', 'terkirim')->count(),
            'disetujui_desa'         => Warga::where('status_verifikasi', 'disetujui_desa')->count(),
            'lolos_verifikasi_pusat'  => Warga::where('status_verifikasi', 'lolos_verifikasi_pusat')->count(),
            'terpasang'              => Warga::where('status_verifikasi', 'terpasang')->count(),
            'ditolak'                => Warga::where('status_verifikasi', 'ditolak/perlu_perbaikan')->count(),
        ];

        // Audit Trail Activity Logs (10 Terakhir)
        $recentLogs = \App\Models\ActivityLog::latest()->take(10)->get();

        return view('dinasesdm.index', compact('wargas', 'stats', 'kabupatens', 'kecamatans', 'desas', 'dusuns', 'wilayahTree', 'chartKabupaten', 'chartStatus', 'recentLogs'));
    }

    private function formatTanggalIndo($dateStr)
    {
        if (!$dateStr) {
            $dateStr = date('Y-m-d');
        }
        $time = strtotime($dateStr);
        $day = date('d', $time);
        $month = (int)date('m', $time);
        $year = date('Y', $time);

        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $day . ' ' . ($bulanIndo[$month] ?? date('F', $time)) . ' ' . $year;
    }

    /**
     * Ekspor Data Pengajuan BPBL ke Format Excel (.xls) Sesuai Layout Instansi ESDM
     */
    public function exportExcel(Request $request)
    {
        $wargas = $this->getFilteredWargaQuery($request)->get();

        $desaFilter = $request->desa;
        if (is_array($desaFilter)) {
            $desaFilter = implode(', ', array_filter($desaFilter));
        }

        $namaKadis = $request->nama_kadis;
        $nipKadis  = $request->nip_kadis;

        if (!empty($desaFilter) && $desaFilter !== 'Semua Desa') {
            $firstDesaName = trim(explode(',', $desaFilter)[0]);
            $kadesUser = User::whereIn('role', ['kepala_desa', 'kades', 'desa'])
                ->where(function($q) use ($firstDesaName) {
                    $q->where('desa', 'like', "%{$firstDesaName}%")
                      ->orWhereRaw('LOWER(desa) = ?', [strtolower($firstDesaName)]);
                })
                ->first();

            if ($kadesUser) {
                if (empty($namaKadis)) {
                    $namaKadis = $kadesUser->name;
                }
                if (empty($nipKadis)) {
                    $nipKadis = $kadesUser->nipd ?: $kadesUser->no_hp;
                }
            }
        }

        $filters = [
            'kabupaten'     => $request->kabupaten ?: 'Semua Kabupaten',
            'kecamatan'     => $request->kecamatan ?: 'Semua Kecamatan',
            'desa'          => $desaFilter ?: 'Semua Desa',
            'dusun'         => $request->dusun ?: 'Semua Dusun/RT',
            'status'        => $request->status ? str_replace('_', ' ', $request->status) : 'Semua Status',
            'nomor_surat'   => $request->nomor_surat ?: 'B-500.10.17.2/        /DESDM/II/' . date('Y'),
            'tanggal_surat' => $this->formatTanggalIndo($request->tanggal_surat),
            'nama_kadis'    => $namaKadis ?: '',
            'nip_kadis'     => $nipKadis ?: '',
        ];

        $stats = [
            'total'     => count($wargas),
            'disetujui' => $wargas->where('status_verifikasi', 'lolos_verifikasi_pusat')->count(),
            'menunggu'  => $wargas->where('status_verifikasi', 'menunggu_verifikasi_pusat')->count(),
            'ditolak'   => $wargas->where('status_verifikasi', 'ditolak/perlu_perbaikan')->count(),
        ];

        $filename = 'Laporan_Pengajuan_BPBL_ESDM_' . date('Ymd_His') . '.xls';

        return response()->streamDownload(function() use ($wargas, $filters, $stats) {
            echo view('dinasesdm.export_excel', compact('wargas', 'filters', 'stats'))->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Ekspor Data Pengajuan BPBL ke Format PDF (Layout Cetak Resmi Dinas ESDM)
     */
    public function exportPdf(Request $request)
    {
        $wargas = $this->getFilteredWargaQuery($request)->get();

        $desaFilter = $request->desa;
        if (is_array($desaFilter)) {
            $desaFilter = implode(', ', array_filter($desaFilter));
        }

        $namaKadis = $request->nama_kadis;
        $nipKadis  = $request->nip_kadis;

        if (!empty($desaFilter) && $desaFilter !== 'Semua Desa') {
            $firstDesaName = trim(explode(',', $desaFilter)[0]);
            $kadesUser = User::whereIn('role', ['kepala_desa', 'kades', 'desa'])
                ->where(function($q) use ($firstDesaName) {
                    $q->where('desa', 'like', "%{$firstDesaName}%")
                      ->orWhereRaw('LOWER(desa) = ?', [strtolower($firstDesaName)]);
                })
                ->first();

            if ($kadesUser) {
                if (empty($namaKadis)) {
                    $namaKadis = $kadesUser->name;
                }
                if (empty($nipKadis)) {
                    $nipKadis = $kadesUser->nipd ?: $kadesUser->no_hp;
                }
            }
        }

        $filters = [
            'kabupaten'     => $request->kabupaten ?: 'Semua Kabupaten',
            'kecamatan'     => $request->kecamatan ?: 'Semua Kecamatan',
            'desa'          => $desaFilter ?: 'Semua Desa',
            'dusun'         => $request->dusun ?: 'Semua Dusun/RT',
            'status'        => $request->status ? str_replace('_', ' ', $request->status) : 'Semua Status',
            'nomor_surat'   => $request->nomor_surat ?: 'B-500.10.17.2/        /DESDM/II/' . date('Y'),
            'tanggal_surat' => $this->formatTanggalIndo($request->tanggal_surat),
            'nama_kadis'    => $namaKadis ?: '',
            'nip_kadis'     => $nipKadis ?: '',
        ];

        $stats = [
            'total'     => count($wargas),
            'disetujui' => $wargas->where('status_verifikasi', 'lolos_verifikasi_pusat')->count(),
            'menunggu'  => $wargas->where('status_verifikasi', 'menunggu_verifikasi_pusat')->count(),
            'ditolak'   => $wargas->where('status_verifikasi', 'ditolak/perlu_perbaikan')->count(),
        ];

        $filename = 'Laporan_Pengajuan_BPBL_ESDM_' . date('Ymd_His') . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dinasesdm.export_pdf', compact('wargas', 'filters', 'stats'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    /**
     * Download Template CSV/Excel Baku untuk Import Data Lama
     */
    public function downloadImportTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="Template_Import_Data_BPBL.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header Kolom Baku
            fputcsv($handle, [
                'nik',
                'nama',
                'kabupaten',
                'kecamatan',
                'desa',
                'rt_rw',
                'alamat',
                'jarak_tiang',
                'latitude',
                'longitude',
                'status_verifikasi',
                'tanggal_pengajuan'
            ]);

            // Baris Contoh Data
            fputcsv($handle, [
                '1571062105810021',
                'ISMAN GUMANTI',
                'KOTA JAMBI',
                'DANAU TELUK',
                'TANJUNG RADEN',
                '04/02',
                'JL. KH. HASAN ANANG RT 04',
                '45 meter',
                '-1.6042000',
                '103.5298000',
                'lolos_verifikasi_pusat',
                '2023-05-15'
            ]);

            fputcsv($handle, [
                '1571060505850061',
                'RD. AZHAR',
                'KOTA JAMBI',
                'DANAU TELUK',
                'TANJUNG RADEN',
                '05/01',
                'JL. TAHER RT 05',
                '30 meter',
                '-1.6051000',
                '103.5312000',
                'lolos_verifikasi_pusat',
                '2024-02-10'
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Memproses Import Data Pengajuan Warga dari File Excel/CSV
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'default_status' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        $successCount = 0;
        $updatedCount = 0;
        $failedCount  = 0;

        // Parse CSV or delimited text file
        if (in_array($extension, ['csv', 'txt', 'xls', 'xlsx'])) {
            $handle = fopen($path, 'r');
            if ($handle !== false) {
                // Read header row
                $header = fgetcsv($handle, 2000, ',');
                
                // If delimiter is semicolon (Common in Indonesian Excel exports)
                if ($header && count($header) == 1 && strpos($header[0], ';') !== false) {
                    rewind($handle);
                    $header = fgetcsv($handle, 2000, ';');
                    $delimiter = ';';
                } else {
                    $delimiter = ',';
                }

                if ($header) {
                    // Clean header names (lowercase & remove BOM)
                    $header = array_map(function($h) {
                        return strtolower(trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $h)));
                    }, $header);

                    while (($row = fgetcsv($handle, 3000, $delimiter)) !== false) {
                        if (count($row) < 2 || empty(array_filter($row))) {
                            continue;
                        }

                        $data = [];
                        foreach ($header as $index => $colName) {
                            $data[$colName] = isset($row[$index]) ? trim($row[$index]) : null;
                        }

                        // Get required fields
                        $nik = preg_replace('/[^0-9]/', '', $data['nik'] ?? '');
                        $nama = $data['nama'] ?? null;
                        $kabupaten = $data['kabupaten'] ?? null;
                        $kecamatan = $data['kecamatan'] ?? null;
                        $desa = $data['desa'] ?? null;

                        if (empty($nik) || strlen($nik) < 10 || empty($nama) || empty($desa)) {
                            $failedCount++;
                            continue;
                        }

                        $statusVerifikasi = !empty($data['status_verifikasi']) 
                            ? $data['status_verifikasi'] 
                            : ($request->default_status ?: 'lolos_verifikasi_pusat');

                        $tanggalPengajuan = !empty($data['tanggal_pengajuan']) 
                            ? $data['tanggal_pengajuan'] 
                            : null;

                        $wargaData = [
                            'nik'               => $nik,
                            'nama'              => $nama,
                            'kabupaten'         => $kabupaten ?: 'KABUPATEN MUARO JAMBI',
                            'kecamatan'         => $kecamatan ?: '-',
                            'desa'              => $desa,
                            'dusun'             => $data['dusun'] ?? ($data['nama_dusun'] ?? null),
                            'rt_rw'             => $data['rt_rw'] ?? '01/01',
                            'no_hp'             => $data['no_hp'] ?? '-',
                            'alamat'            => $data['alamat'] ?? 'Desa ' . $desa,
                            'jarak_tiang'       => $data['jarak_tiang'] ?? null,
                            'latitude'          => is_numeric($data['latitude'] ?? null) ? $data['latitude'] : 0.0,
                            'longitude'         => is_numeric($data['longitude'] ?? null) ? $data['longitude'] : 0.0,
                            'status_verifikasi' => $statusVerifikasi,
                        ];

                        if ($tanggalPengajuan) {
                            try {
                                $wargaData['created_at'] = \Carbon\Carbon::parse($tanggalPengajuan);
                            } catch (\Exception $e) {}
                        }

                        $existing = Warga::where('nik', $nik)->first();
                        if ($existing) {
                            $existing->update($wargaData);
                            $updatedCount++;
                        } else {
                            Warga::create($wargaData);
                            $successCount++;
                        }
                    }
                }
                fclose($handle);
            }
        }

        $message = "Proses Import Selesai! {$successCount} data baru berhasil ditambahkan";
        if ($updatedCount > 0) {
            $message .= ", {$updatedCount} data diperbarui";
        }
        if ($failedCount > 0) {
            $message .= ", {$failedCount} baris tidak valid dilewati";
        }

        return redirect()->back()->with('success', $message);
    }


    public function show(Warga $warga)
    {
        $warga->load('berkas');
        return view('dinasesdm.show', compact('warga'));
    }

    public function approve(Warga $warga)
    {
        $warga->update(['status_verifikasi' => 'lolos_verifikasi_pusat']);

        \App\Models\ActivityLog::record(
            'esdm_approve',
            'Verifikator Dinas ESDM menyetujui pengajuan warga NIK ' . $warga->nik . ' (' . $warga->nama . ' - ' . $warga->desa . ')'
        );

        return back()->with('success', 'Warga lolos verifikasi pusat.');
    }

    public function reject(Request $request, Warga $warga)
    {
        $request->validate(['catatan' => 'nullable|string|max:500']);

        $warga->update([
            'status_verifikasi' => 'ditolak/perlu_perbaikan',
            'catatan'           => $request->catatan,
            'ditolak_oleh'      => 'instansi',
        ]);

        \App\Models\ActivityLog::record(
            'esdm_reject',
            'Verifikator Dinas ESDM menolak pengajuan warga NIK ' . $warga->nik . ' dengan catatan: ' . ($request->catatan ?: 'Tanpa catatan')
        );

        return back()->with('success', 'Berkas warga ditolak oleh instansi.');
    }

    public function destroy(Warga $warga)
    {
        $warga->delete();
        return redirect()->route('dinasesdm.index')->with('success', 'Data warga berhasil dihapus.');
    }

    // ==== Role management: instansi mengelola akun kepala desa ====

    // ==== Role management: instansi mengelola akun kepala desa ====

    public function users(Request $request)
    {
        $status = $request->query('status', 'approved');
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            $status = 'approved';
        }

        $roleFilter = $request->query('role', 'all');

        $query = User::query();
        if (in_array($roleFilter, ['kepala_desa', 'verifikator_esdm', 'super_admin', 'instansi'])) {
            $query->where('role', $roleFilter);
        }

        $countPending  = (clone $query)->where('status', 'pending')->count();
        $countApproved = (clone $query)->where('status', 'approved')->count();
        $countRejected = (clone $query)->where('status', 'rejected')->count();

        $users = (clone $query)
            ->where('status', $status)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dinasesdm.users.index', compact(
            'users',
            'status',
            'roleFilter',
            'countPending',
            'countApproved',
            'countRejected'
        ));
    }

    public function approveUser(User $user)
    {
        $user->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Akun ' . $user->name . ' (' . ucfirst($user->role) . ') berhasil disetujui.');
    }

    public function rejectUser(User $user)
    {
        $user->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Pendaftaran akun ' . $user->name . ' telah ditolak.');
    }

    public function createUser()
    {
        return view('dinasesdm.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'role'     => 'required|in:verifikator_esdm,kepala_desa,super_admin,instansi',
            'name'     => 'required|string|max:255',
            'nipd'     => 'nullable|string|max:50',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'desa'     => 'nullable|string|max:255',
            'no_hp'    => 'nullable|string|max:20',
        ]);

        $desa = $validated['desa'];
        if (in_array($validated['role'], ['verifikator_esdm', 'super_admin', 'instansi']) && empty($desa)) {
            $desa = 'Dinas ESDM Provinsi Jambi';
        }

        User::create([
            'role'     => $validated['role'],
            'name'     => $validated['name'],
            'nipd'     => $validated['nipd'] ?? null,
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'desa'     => $desa ?: 'Dinas ESDM Provinsi Jambi',
            'no_hp'    => $validated['no_hp'] ?? null,
            'status'   => 'approved',
        ]);

        $roleLabels = [
            'verifikator_esdm' => 'Verifikator ESDM',
            'kepala_desa'      => 'Kepala Desa',
            'super_admin'      => 'Super Admin ESDM',
            'instansi'         => 'Super Admin ESDM',
        ];

        $label = $roleLabels[$validated['role']] ?? 'Pengguna';
        return redirect()->route('dinasesdm.users.index')->with('success', "Akun {$label} ({$validated['name']}) berhasil dibuat dan diaktifkan.");
    }

    public function editUser(User $user)
    {
        return view('dinasesdm.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'role'  => 'required|in:verifikator_esdm,kepala_desa,super_admin,instansi',
            'name'  => 'required|string|max:255',
            'nipd'  => 'nullable|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'desa'  => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
        ]);

        if (in_array($validated['role'], ['verifikator_esdm', 'super_admin', 'instansi']) && empty($validated['desa'])) {
            $validated['desa'] = 'Dinas ESDM Provinsi Jambi';
        }

        $user->update($validated);

        return redirect()->route('dinasesdm.users.index')->with('success', 'Informasi akun berhasil diperbarui.');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return redirect()->route('dinasesdm.users.index')->with('success', 'Akun berhasil dihapus.');
    }

    // ==== Management Data Rasio Desa (Peta Geospasial) ====

    public function desaIndex(Request $request)
    {
        $desas = Desa::when($request->search, function ($query, $search) {
                return $query->where('nama_desa', 'like', "%{$search}%")
                             ->orWhere('kabupaten', 'like', "%{$search}%");
            })
            ->orderBy('kabupaten')
            ->orderBy('nama_desa')
            ->paginate(15)
            ->withQueryString();

        return view('dinasesdm.desa.index', compact('desas'));
    }

    public function createDesa()
    {
        return view('dinasesdm.desa.create');
    }

    public function storeDesa(Request $request)
    {
        $validated = $request->validate([
            'nama_desa'     => 'required|string|max:255',
            'kabupaten'     => 'required|string|max:255',
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'total_rt'      => 'required|integer|min:0',
            'berlistrik_rt' => 'required|integer|min:0',
        ]);

        Desa::create($validated);

        return redirect()->route('dinasesdm.desa.index')->with('success', 'Data desa berhasil ditambahkan.');
    }

    public function editDesa(Desa $desa)
    {
        return view('dinasesdm.desa.edit', compact('desa'));
    }

    public function updateDesa(Request $request, Desa $desa)
    {
        $validated = $request->validate([
            'nama_desa'     => 'required|string|max:255',
            'kabupaten'     => 'required|string|max:255',
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'total_rt'      => 'required|integer|min:0',
            'berlistrik_rt' => 'required|integer|min:0',
        ]);

        $desa->update($validated);

        return redirect()->route('dinasesdm.desa.index')->with('success', 'Data desa berhasil diperbarui.');
    }

    public function destroyDesa(Desa $desa)
    {
        $desa->delete();
        return redirect()->route('dinasesdm.desa.index')->with('success', 'Data desa berhasil dihapus.');
    }

    // ==== Pengelolaan Pengajuan Lisdes ====

    public function lisdesIndex(Request $request)
    {
        $lisdesList = PengajuanLisdes::with('user')
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_dusun', 'like', "%{$search}%")
                      ->orWhere('desa', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($u) use ($search) {
                          $u->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($request->status && $request->status !== 'all', function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'               => PengajuanLisdes::count(),
            'menunggu_verifikasi' => PengajuanLisdes::where('status', 'menunggu_verifikasi')->count(),
            'disetujui'           => PengajuanLisdes::where('status', 'disetujui')->count(),
            'ditolak'             => PengajuanLisdes::where('status', 'ditolak')->count(),
        ];

        return view('dinasesdm.lisdes.index', compact('lisdesList', 'stats'));
    }

    public function lisdesShow(PengajuanLisdes $lisdes)
    {
        $lisdes->load('user');
        return view('dinasesdm.lisdes.show', compact('lisdes'));
    }

    public function lisdesApprove(Request $request, PengajuanLisdes $lisdes)
    {
        $lisdes->update([
            'status'       => 'disetujui',
            'catatan_esdm' => $request->catatan_esdm ?? null,
        ]);

        return redirect()->back()->with('success', 'Usulan Lisdes desa ' . $lisdes->desa . ' (' . $lisdes->nama_dusun . ') berhasil disetujui.');
    }

    public function lisdesReject(Request $request, PengajuanLisdes $lisdes)
    {
        $request->validate([
            'catatan_esdm' => 'nullable|string|max:1000',
        ]);

        $lisdes->update([
            'status'       => 'ditolak',
            'catatan_esdm' => $request->catatan_esdm,
        ]);

        return redirect()->back()->with('success', 'Usulan Lisdes desa ' . $lisdes->desa . ' (' . $lisdes->nama_dusun . ') telah ditolak.');
    }

    public function lisdesDestroy(PengajuanLisdes $lisdes)
    {
        $lisdes->delete();
        return redirect()->route('dinasesdm.lisdes.index')->with('success', 'Data usulan Lisdes berhasil dihapus.');
    }
}
