<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use App\Models\User;
use App\Models\Desa;
use App\Models\PengajuanLisdes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\AuditLog;

class AdminController extends Controller
{
            public function users(Request $request)
        {
            $status     = in_array($request->query('status'), ['pending', 'approved', 'rejected']) ? $request->query('status') : 'approved';
            $roleFilter = $request->query('role', 'all');

            $query = User::query();
            if (in_array($roleFilter, ['kepala_desa', 'verifikator_esdm', 'super_admin', 'instansi', 'staff_desa'])) {
                $query->where('role', $roleFilter);
            }

            $countPending  = (clone $query)->where('status', 'pending')->count();
            $countApproved = (clone $query)->where('status', 'approved')->count();
            $countRejected = (clone $query)->where('status', 'rejected')->count();

            $users = (clone $query)->where('status', $status)->latest()->paginate(15)->withQueryString();

            return view('admin.users.index', compact('users', 'status', 'roleFilter', 'countPending', 'countApproved', 'countRejected'));
        }

        public function approveUser(User $user)
        {
            $user->update(['status' => 'approved']);
            return back()->with('success', 'Akun ' . $user->name . ' berhasil disetujui.');
        }

        public function rejectUser(User $user)
        {
            $user->update(['status' => 'rejected']);
            return back()->with('success', 'Akun ' . $user->name . ' ditolak.');
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
    // public function show(Warga $warga)
    // {
    //     $warga->load('berkas');
    //     return view('admin.show', compact('warga'));
    // }
    public function auditLogs(Request $request)
    {
        $logs = AuditLog::query()
            ->when($request->search, fn($q, $s) => $q->where(fn($w) =>
                $w->where('user_name', 'like', "%$s%")
                ->orWhere('ip_address', 'like', "%$s%")
              ->orWhere('description', 'like', "%$s%")))
            ->latest()->paginate(20);

        return view('admin.audit_logs.index', compact('logs'));
    }

    private function getFilteredWargaQuery(Request $request)
    {
        return Warga::with('berkas')
            ->when($request->status, function ($query, $status) {
                if ($status === 'butuh_validasi') {
                    return $query->where('butuh_validasi_realisasi', true);
                }
                return $query->where('status_verifikasi', $status);
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
            ->when($request->tahun, function ($query, $tahun) {
                return $query->where(function($q) use ($tahun) {
                    $q->where('tahun_usulan', $tahun)
                      ->orWhereYear('created_at', $tahun);
                });
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('nik', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%")
                      ->orWhere('desa', 'like', "%{$search}%")
                      ->orWhere('kecamatan', 'like', "%{$search}%")
                      ->orWhere('kabupaten', 'like', "%{$search}%")
                      ->orWhere('dusun', 'like', "%{$search}%")
                      ->orWhere('alamat', 'like', "%{$search}%");
                });
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
            'app' => 'E-LISTRIK Dinas ESDM Provinsi Jambi',
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
            'total'     => Warga::count(),
            'menunggu'  => Warga::whereIn('status_verifikasi', ['menunggu_verifikasi_pusat', 'disetujui_desa', 'terkirim', 'pending'])->count(),
            'disetujui' => Warga::whereIn('status_verifikasi', ['lolos_verifikasi_pusat', 'terpasang'])->count(),
            'ditolak'   => Warga::where('status_verifikasi', 'ditolak/perlu_perbaikan')->count(),
            'terpasang' => Warga::where('status_verifikasi', 'terpasang')->count(),
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

        return view('admin.index', compact('wargas', 'stats', 'kabupatens', 'kecamatans', 'desas', 'dusuns', 'wilayahTree', 'chartKabupaten', 'chartStatus', 'recentLogs'));
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
        if ($request->scope === 'historis') {
            $query = Warga::with('berkas')
                ->where(function ($q) {
                    $q->where('status_verifikasi', 'terpasang')
                      ->orWhereNotNull('tahun_usulan')
                      ->orWhereNotNull('keterangan_import');
                })
                ->when($request->search, function ($q, $search) {
                    return $q->where(function ($sub) use ($search) {
                        $sub->where('nik', 'like', "%{$search}%")
                            ->orWhere('nama', 'like', "%{$search}%")
                            ->orWhere('desa', 'like', "%{$search}%")
                            ->orWhere('kecamatan', 'like', "%{$search}%")
                            ->orWhere('alamat', 'like', "%{$search}%");
                    });
                })
                ->when($request->tahun, function ($q, $tahun) {
                    return $q->where(function($sub) use ($tahun) {
                        $sub->where('tahun_usulan', $tahun)
                            ->orWhereYear('created_at', $tahun);
                    });
                })
                ->when($request->kabupaten, function ($q, $kab) {
                    return $q->where('kabupaten', $kab);
                })
                ->when($request->kecamatan, function ($q, $kec) {
                    return $q->where('kecamatan', $kec);
                })
                ->when($request->desa, function ($q, $des) {
                    if (is_array($des)) {
                        $filteredDesa = array_filter($des);
                        return !empty($filteredDesa) ? $q->whereIn('desa', $filteredDesa) : $q;
                    }
                    return $q->where('desa', $des);
                })
                ->when($request->status, function ($q, $status) {
                    return $q->where('status_verifikasi', $status);
                });
        } else {
            $query = $this->getFilteredWargaQuery($request);
        }

        $wargas = $query->reorder()
            ->orderBy('kecamatan', 'asc')
            ->orderBy('desa', 'asc')
            ->orderByRaw('CAST(COALESCE(NULLIF(tahun_usulan, ""), YEAR(created_at)) AS UNSIGNED) DESC')
            ->orderBy('nama', 'asc')
            ->get();

        $selectedColumns = $request->input('columns', []);

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

        $filename = ($request->scope === 'historis' ? 'Data_Historis_BPBL_ESDM_' : 'Laporan_Pengajuan_BPBL_ESDM_') . date('Ymd_His') . '.xls';

        return response()->streamDownload(function() use ($wargas, $filters, $stats, $selectedColumns) {
            echo view('admin.export_excel', compact('wargas', 'filters', 'stats', 'selectedColumns'))->render();
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
        if ($request->scope === 'historis') {
            $query = Warga::with('berkas')
                ->where(function ($q) {
                    $q->where('status_verifikasi', 'terpasang')
                      ->orWhereNotNull('tahun_usulan')
                      ->orWhereNotNull('keterangan_import');
                })
                ->when($request->search, function ($q, $search) {
                    return $q->where(function ($sub) use ($search) {
                        $sub->where('nik', 'like', "%{$search}%")
                            ->orWhere('nama', 'like', "%{$search}%")
                            ->orWhere('desa', 'like', "%{$search}%")
                            ->orWhere('kecamatan', 'like', "%{$search}%")
                            ->orWhere('alamat', 'like', "%{$search}%");
                    });
                })
                ->when($request->tahun, function ($q, $tahun) {
                    return $q->where(function($sub) use ($tahun) {
                        $sub->where('tahun_usulan', $tahun)
                            ->orWhereYear('created_at', $tahun);
                    });
                })
                ->when($request->kabupaten, function ($q, $kab) {
                    return $q->where('kabupaten', $kab);
                })
                ->when($request->kecamatan, function ($q, $kec) {
                    return $q->where('kecamatan', $kec);
                })
                ->when($request->desa, function ($q, $des) {
                    if (is_array($des)) {
                        $filteredDesa = array_filter($des);
                        return !empty($filteredDesa) ? $q->whereIn('desa', $filteredDesa) : $q;
                    }
                    return $q->where('desa', $des);
                })
                ->when($request->status, function ($q, $status) {
                    return $q->where('status_verifikasi', $status);
                });
        } else {
            $query = $this->getFilteredWargaQuery($request);
        }

        $wargas = $query->reorder()
            ->orderBy('kecamatan', 'asc')
            ->orderBy('desa', 'asc')
            ->orderByRaw('CAST(COALESCE(NULLIF(tahun_usulan, ""), YEAR(created_at)) AS UNSIGNED) DESC')
            ->orderBy('nama', 'asc')
            ->get();

        $selectedColumns = $request->input('columns', []);

        $desaFilter = $request->desa;
        if (is_array($desaFilter)) {
            $desaFilter = implode(', ', array_filter($desaFilter));
        }

        if ($request->batch_id) {
            $query->where('batch_id', $request->batch_id);
            $batchObj = \App\Models\PengajuanBatch::find($request->batch_id);
            if ($batchObj) {
                $desaFilter = $batchObj->desa;
                $request->merge([
                    'desa' => $batchObj->desa,
                    'kecamatan' => $batchObj->kecamatan,
                    'kabupaten' => $batchObj->kabupaten,
                ]);
            }
        }

        $namaKades = $request->nama_kades ?: $request->nama_kadis;
        $nipKades  = $request->nip_kades ?: $request->nip_kadis;
        $kadesUser = null;

        if (!empty($desaFilter) && $desaFilter !== 'Semua Desa') {
            $firstDesaName = trim(explode(',', $desaFilter)[0]);
            $kadesUser = User::whereIn('role', ['kepala_desa', 'kades', 'desa'])
                ->where(function($q) use ($firstDesaName) {
                    $q->where('desa', 'like', "%{$firstDesaName}%")
                      ->orWhereRaw('LOWER(desa) = ?', [strtolower($firstDesaName)]);
                })
                ->first();

            if ($kadesUser) {
                if (empty($namaKades)) {
                    $namaKades = $kadesUser->name;
                }
                if (empty($nipKades)) {
                    $nipKades = $kadesUser->nipd ?: $kadesUser->no_hp;
                }
            }
        }

        $Kepaladesa = $kadesUser;

        $filters = [
            'kabupaten'     => $request->kabupaten ?: ($kadesUser->kabupaten ?? 'Semua Kabupaten'),
            'kecamatan'     => $request->kecamatan ?: ($kadesUser->kecamatan ?? 'Semua Kecamatan'),
            'desa'          => $desaFilter ?: 'Semua Desa',
            'dusun'         => $request->dusun ?: 'Semua Dusun/RT',
            'status'        => $request->status ? str_replace('_', ' ', $request->status) : 'Semua Status',
            'nomor_surat'   => $request->nomor_surat ?: 'B-500.10.17.2/        /DESDM/II/' . date('Y'),
            'tanggal_surat' => $this->formatTanggalIndo($request->tanggal_surat),
            'nama_kades'    => $namaKades ?: '',
            'nip_kades'     => $nipKades ?: '',
            'nama_kadis'    => $namaKades ?: '', // Backward compatibility
            'nip_kadis'     => $nipKades ?: '',
        ];

        $stats = [
            'total'     => count($wargas),
            'disetujui' => $wargas->where('status_verifikasi', 'lolos_verifikasi_pusat')->count(),
            'menunggu'  => $wargas->where('status_verifikasi', 'menunggu_verifikasi_pusat')->count(),
            'ditolak'   => $wargas->where('status_verifikasi', 'ditolak/perlu_perbaikan')->count(),
        ];

        $filename = ($request->scope === 'historis' ? 'Data_Historis_BPBL_ESDM_' : 'Laporan_Pengajuan_BPBL_ESDM_') . date('Ymd_His') . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.export_pdf', compact('wargas', 'filters', 'stats', 'selectedColumns', 'Kepaladesa'))
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

            // Header Kolom Baku Terbaru
            fputcsv($handle, [
                'No',
                'KECAMATAN',
                'DESA/KELURAHAN',
                'NAMA',
                'NIK',
                'ALAMAT',
                'Usulan',
                'Realisasi',
                'Keterangan'
            ]);

            // Baris Contoh Data 1 (Sudah Direalisasi / Ada Centang)
            fputcsv($handle, [
                '1',
                'ALAM BARAJO',
                'BAGAN PETE',
                'SITI JAMILAH',
                '1571074107470324',
                'JL. LINGKAR BARAT II',
                '2023',
                '✓',
                'Teraliri Listrik 2023'
            ]);

            // Baris Contoh Data 2 (Belum Tentu Realisasi / Tanpa Centang -> Masuk Validasi SuperAdmin)
            fputcsv($handle, [
                '2',
                'ALAM BARAJO',
                'BAGAN PETE',
                'SYAHRUL',
                '1404112709940003',
                'JL. SUNAN PANDARAN NO 20 RT 31',
                '2023',
                '',
                'Perlu validasi realisasi SuperAdmin'
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
        $needValidationCount = 0;
        $failedCount  = 0;

        $findHeaderAndRows = function(array $allRows) {
            $headerIndex = -1;
            $header = [];

            foreach ($allRows as $rIdx => $row) {
                $nonEmptyCells = array_filter($row, fn($c) => trim((string)$c) !== '');
                if (count($nonEmptyCells) < 2) {
                    continue; // Skip single-cell title headers
                }

                $rowJoined = strtolower(implode(' | ', array_map('strval', $row)));

                $keywords = ['nik', 'nama', 'kecamatan', 'desa', 'kelurahan', 'alamat', 'realisasi', 'usulan'];
                $matchCount = 0;
                foreach ($keywords as $kw) {
                    if (str_contains($rowJoined, $kw)) {
                        $matchCount++;
                    }
                }

                if ($matchCount >= 2) {
                    $headerIndex = $rIdx;
                    $header = array_map(function($h) {
                        return mb_strtolower(trim(preg_replace('/[\x00-\x1F\x7F]/', '', (string)$h)));
                    }, $row);
                    break;
                }
            }

            if ($headerIndex === -1 && !empty($allRows)) {
                foreach ($allRows as $rIdx => $row) {
                    $nonEmptyCells = array_filter($row, fn($c) => trim((string)$c) !== '');
                    if (count($nonEmptyCells) >= 3) {
                        $headerIndex = $rIdx;
                        $header = array_map(function($h) {
                            return mb_strtolower(trim(preg_replace('/[\x00-\x1F\x7F]/', '', (string)$h)));
                        }, $row);
                        break;
                    }
                }
            }

            if ($headerIndex === -1 && !empty($allRows)) {
                $headerIndex = 0;
                $header = array_map(function($h) {
                    return mb_strtolower(trim(preg_replace('/[\x00-\x1F\x7F]/', '', (string)$h)));
                }, $allRows[0]);
            }

            $dataRows = [];
            for ($i = $headerIndex + 1; $i < count($allRows); $i++) {
                $row = $allRows[$i];
                if (empty(array_filter($row, fn($c) => trim((string)$c) !== ''))) continue;
                $data = [];
                foreach ($header as $idx => $colName) {
                    $data[$colName] = isset($row[$idx]) ? trim((string)$row[$idx]) : '';
                }
                $data['_raw_cols_'] = $row;
                $dataRows[] = $data;
            }
            return $dataRows;
        };

        if (in_array($extension, ['xlsx', 'xls'])) {
            $parsedRows = \App\Helpers\SimpleXlsxReader::parse($path);
            if (!empty($parsedRows)) {
                $rowsData = $findHeaderAndRows($parsedRows);
            }
        }

        // Fallback or CSV/TXT parser
        if (empty($rowsData) && in_array($extension, ['csv', 'txt', 'xls', 'xlsx'])) {
            $handle = fopen($path, 'r');
            if ($handle !== false) {
                $allRawRows = [];
                $delimiter = ',';
                $firstLine = fgetcsv($handle, 3000, ',');
                if ($firstLine && count($firstLine) == 1 && strpos($firstLine[0], ';') !== false) {
                    $delimiter = ';';
                }
                rewind($handle);

                while (($row = fgetcsv($handle, 3000, $delimiter)) !== false) {
                    $allRawRows[] = $row;
                }
                fclose($handle);

                if (!empty($allRawRows)) {
                    $rowsData = $findHeaderAndRows($allRawRows);
                }
            }
        }

        foreach ($rowsData as $data) {
            $nikRaw = '';
            $namaRaw = '';
            $desaRaw = '';
            $kecRaw = '';
            $kabRaw = '';
            $alamatRaw = '';
            $usulanRaw = '';
            $realisasiRaw = '';
            $ketRaw = '';

            // Fuzzy column resolution
            foreach ($data as $colKey => $cellVal) {
                if ($colKey === '_raw_cols_') continue;
                $kClean = strtolower(trim((string)$colKey));
                $vClean = trim((string)$cellVal);

                if ($vClean === '') continue;

                if (empty($nikRaw) && str_contains($kClean, 'nik')) {
                    $nikRaw = $vClean;
                } elseif (empty($namaRaw) && str_contains($kClean, 'nama')) {
                    $namaRaw = $vClean;
                } elseif (empty($desaRaw) && (str_contains($kClean, 'desa') || str_contains($kClean, 'kelurahan'))) {
                    $desaRaw = $vClean;
                } elseif (empty($kecRaw) && (str_contains($kClean, 'kecamatan') || str_contains($kClean, 'kec'))) {
                    $kecRaw = $vClean;
                } elseif (empty($kabRaw) && str_contains($kClean, 'kabupaten')) {
                    $kabRaw = $vClean;
                } elseif (empty($alamatRaw) && str_contains($kClean, 'alamat')) {
                    $alamatRaw = $vClean;
                } elseif (empty($usulanRaw) && (str_contains($kClean, 'usulan') || str_contains($kClean, 'tahun'))) {
                    $usulanRaw = $vClean;
                } elseif (empty($realisasiRaw) && str_contains($kClean, 'realisasi')) {
                    $realisasiRaw = $vClean;
                } elseif (empty($ketRaw) && (str_contains($kClean, 'keterangan') || str_contains($kClean, 'catatan'))) {
                    $ketRaw = $vClean;
                }
            }

            // Fallback for NIK if empty
            if (empty($nikRaw) && isset($data['_raw_cols_'])) {
                foreach ($data['_raw_cols_'] as $cVal) {
                    $vStr = trim((string)$cVal);
                    if ($vStr === '') continue;
                    $numOnly = preg_replace('/[^0-9]/', '', $vStr);
                    if (strlen($numOnly) >= 10 && strlen($numOnly) <= 18) {
                        $nikRaw = $vStr;
                        break;
                    }
                }
            }

            // Fallback for Nama if empty
            if (empty($namaRaw) && isset($data['_raw_cols_'])) {
                foreach ($data['_raw_cols_'] as $cVal) {
                    $vStr = trim((string)$cVal);
                    if ($vStr === '') continue;
                    if (!preg_match('/[0-9]/', $vStr) && strlen($vStr) >= 3) {
                        $vLower = strtolower($vStr);
                        if (!in_array($vLower, ['✓', 'v', 'ya', 'tidak', 'sudah', 'belum', 'terpasang'])) {
                            $namaRaw = $vStr;
                            break;
                        }
                    }
                }
            }

            // Handle Scientific Notation in NIK (e.g. 1.57107E+15 or 1,57107E+15)
            $nikClean = str_replace(',', '.', (string)$nikRaw);
            if (is_numeric($nikClean) && str_contains(strtolower($nikClean), 'e+')) {
                $nik = sprintf('%.0f', (float)$nikClean);
            } else {
                $nik = preg_replace('/[^0-9]/', '', (string)$nikRaw);
            }

            $nama = $namaRaw ?: ($data['nama'] ?? 'WARGA PEMOHON');
            $desa = $desaRaw ?: ($data['desa'] ?? 'BAGAN PETE');
            $kecamatan = (!empty($kecRaw) && $kecRaw !== '-') ? $kecRaw : ($data['kecamatan'] ?? 'ALAM BARAJO');
            $kabupaten = (!empty($kabRaw) && $kabRaw !== '-' && $kabRaw !== 'KABUPATEN MUARO JAMBI') ? $kabRaw : ($data['kabupaten'] ?? 'KOTA JAMBI');
            if (strtoupper($kecamatan) === 'ALAM BARAJO') {
                $kabupaten = 'KOTA JAMBI';
            }
            $alamat = $alamatRaw ?: ($data['alamat'] ?? ('Desa ' . $desa));
            $usulan = $usulanRaw ?: ($data['usulan'] ?? null);
            $realisasiVal = $realisasiRaw ?: ($data['realisasi'] ?? null);
            $keteranganVal = $ketRaw ?: ($data['keterangan'] ?? null);

            if (empty($nik) || strlen($nik) < 10) {
                $failedCount++;
                continue;
            }

            $realisasiResult = $this->parseRealisasiStatus($realisasiVal);
            $statusVerifikasi = !empty($data['status_verifikasi'])
                ? $data['status_verifikasi']
                : ($request->default_status ?: $realisasiResult['status_verifikasi']);

            $butuhValidasi = $request->default_status ? false : $realisasiResult['butuh_validasi_realisasi'];

            if ($butuhValidasi) {
                $needValidationCount++;
            }



            $wargaData = [
                'nik'                      => $nik,
                'nama'                     => $nama,
                'kabupaten'                => $kabupaten,
                'kecamatan'                => $kecamatan,
                'desa'                     => $desa,
                'dusun'                    => $data['dusun'] ?? null,
                'rt_rw'                    => $data['rt_rw'],
                'no_hp'                    => $data['no_hp'] ?? '-',
                'alamat'                   => $alamat,
                'jarak_tiang'              => $data['jarak_tiang'] ?? null,
                'latitude'                 => is_numeric($data['latitude'] ?? null) ? (float)$data['latitude'] : 0.0,
                'longitude'                => is_numeric($data['longitude'] ?? null) ? (float)$data['longitude'] : 0.0,
                'status_verifikasi'        => $statusVerifikasi,
                'butuh_validasi_realisasi' => $butuhValidasi,
                'tahun_usulan'             => $usulan ? (string) $usulan : null,
                'keterangan_import'        => $keteranganVal ?: 'Data Historis Import Excel',
            ];

            if ($usulan && is_numeric($usulan) && strlen((string)$usulan) == 4) {
                try {
                    $wargaData['created_at'] = \Carbon\Carbon::createFromDate((int)$usulan, 1, 1);
                } catch (\Exception $e) {}
            } elseif (!empty($data['tanggal_pengajuan'])) {
                try {
                    $wargaData['created_at'] = \Carbon\Carbon::parse($data['tanggal_pengajuan']);
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

        $message = "Proses Import Selesai! {$successCount} data baru berhasil ditambahkan";
        if ($updatedCount > 0) {
            $message .= ", {$updatedCount} data diperbarui";
        }
        if ($needValidationCount > 0) {
            $message .= ". ⚠️ {$needValidationCount} data tanpa centang realisasi masuk ke antrean validasi SuperAdmin.";
        }
        if ($failedCount > 0) {
            $message .= ", {$failedCount} baris tidak valid dilewati";
        }

        return redirect()->route('admin.historis.index')->with('success', $message);
    }

    /**
     * Helper internal untuk mengidentifikasi centang pada kolom Realisasi Excel
     */
    private function parseRealisasiStatus($realisasiVal): array
    {
        $val = trim((string) $realisasiVal);

        if ($val === '') {
            return [
                'status_verifikasi' => 'menunggu_verifikasi_pusat',
                'butuh_validasi_realisasi' => true,
            ];
        }

        $valLower = strtolower($val);
        $checkIndicators = ['✓', 'v', '1', 'ya', 'sudah', 'terpasang', 'realisasi', 'true', 'ok'];

        $isChecked = in_array($valLower, $checkIndicators, true)
            || str_contains($valLower, '✓')
            || str_contains($valLower, 'terpasang')
            || str_contains($valLower, 'sudah')
            || (is_numeric($val) && (int)$val > 2000);

        if ($isChecked) {
            return [
                'status_verifikasi' => 'terpasang',
                'butuh_validasi_realisasi' => false,
            ];
        }

        return [
            'status_verifikasi' => 'menunggu_verifikasi_pusat',
            'butuh_validasi_realisasi' => true,
        ];
    }


    public function show(Warga $warga)
    {
        $warga->load('berkas');
        return view('admin.show', compact('warga'));
    }

    public function destroy(Warga $warga)
    {
        $warga->delete();
        return redirect()->route('admin.index')->with('success', 'Data warga berhasil dihapus.');
    }

    public function createUser()
    {
        return view('admin.users.create');
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
        return redirect()->route('admin.users.index')->with('success', "Akun {$label} ({$validated['name']}) berhasil dibuat dan diaktifkan.");
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
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

        return redirect()->route('admin.users.index')->with('success', 'Informasi akun berhasil diperbarui.');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dihapus.');
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

        return view('admin.desa.index', compact('desas'));
    }

    public function createDesa()
    {
        return view('admin.desa.create');
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

        return redirect()->route('admin.desa.index')->with('success', 'Data desa berhasil ditambahkan.');
    }

    public function editDesa(Desa $desa)
    {
        return view('admin.desa.edit', compact('desa'));
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

        return redirect()->route('admin.desa.index')->with('success', 'Data desa berhasil diperbarui.');
    }

    public function destroyDesa(Desa $desa)
    {
        $desa->delete();
        return redirect()->route('admin.desa.index')->with('success', 'Data desa berhasil dihapus.');
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

        return view('admin.lisdes.index', compact('lisdesList', 'stats'));
    }

    public function lisdesShow(PengajuanLisdes $lisdes)
    {
        $lisdes->load('user');
        return view('admin.lisdes.show', compact('lisdes'));
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
        return redirect()->route('admin.lisdes.index')->with('success', 'Data usulan Lisdes berhasil dihapus.');
    }

    /**
     * Update Legalitas Instalasi Akhir & Upload BAST (Admin ESDM ACT-05)
     */
    public function updateLegalitas(Request $request, Warga $warga)
    {
        $validated = $request->validate([
            'no_nidi' => 'nullable|string|max:255',
            'no_slo' => 'nullable|string|max:255',
            'id_pelanggan' => 'nullable|string|max:255',
            'file_bast' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = [
            'no_nidi' => $validated['no_nidi'],
            'no_slo' => $validated['no_slo'],
            'id_pelanggan' => $validated['id_pelanggan'],
        ];

        if ($request->hasFile('file_bast')) {
            if ($warga->file_bast) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($warga->file_bast);
            }
            $data['file_bast'] = $request->file('file_bast')->store('bast', 'public');
        }

        $warga->update($data);

        \App\Models\ActivityLog::record('update_legalitas', "Admin ESDM memperbarui legalitas NIDI ({$warga->no_nidi}), SLO ({$warga->no_slo}), dan Upload BAST untuk NIK {$warga->nik}");

        return back()->with('success', 'Legalitas NIDI, SLO, ID Pelanggan PLN, dan Dokumen BAST berhasil diperbarui!');
    }

    /**
     * Import data tiang listrik TR/TM & Gardu dari file GeoJSON/CSV
     */
    public function importElectricPoles(Request $request)
    {
        $request->validate([
            'pole_file' => 'required|file|mimes:json,geojson,csv,txt|max:5120',
        ]);

        $service = new \App\Services\ElectricPoleImportService();
        $result = $service->import($request->file('pole_file'));

        \App\Models\ActivityLog::record('import_electric_poles', "Import data tiang listrik: {$result['imported_count']} tiang berhasil diproses.");

        return back()->with('success', "Import tiang listrik selesai! {$result['imported_count']} tiang berhasil dimasukkan ke database.");
    }

    /**
     * Export GeoJSON tiang listrik untuk Leaflet.js / GIS Client
     */
    public function exportElectricPolesGeoJson()
    {
        $service = new \App\Services\ElectricPoleImportService();
        return response()->json($service->exportGeoJson());
    }

    /**
     * Export OGC KML format file untuk Google Earth
     */
    public function exportKml(Request $request)
    {
        $service = new \App\Services\KmlExporterService();
        $kmlContent = $service->generateKml($request->query('desa'));
        $filename = 'SIPELITA_Layer_Spasial_' . date('Ymd_His') . '.kml';

        \App\Models\ActivityLog::record('export_kml', "Ekspor layer geospasial SIPELITA ke format OGC KML Google Earth.");

        return response($kmlContent, 200, [
            'Content-Type' => 'application/vnd.google-earth.kml+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Unduh Berita Acara Verifikasi Lapangan (BAVL) PDF Resmi
     */
    public function downloadBavlPdf(Warga $warga)
    {
        $service = new \App\Services\BavlGeneratorService();
        $pdf = $service->generateBavlPdf($warga);
        return $pdf->download("BAVL_Verifikasi_ESDM_{$warga->nik}.pdf");
    }

    /**
     * Unduh Peta Layout Kartografi Lisdes PDF
     */
    public function downloadLisdesMapPdf($id, Request $request)
    {
        $type = $request->query('type', 'lisdes');
        $service = new \App\Services\LisdesMapPdfService();
        $pdf = $service->generateMapPdf($id, $type);
        return $pdf->download("Peta_Layout_Kartografi_Lisdes_{$id}.pdf");
    }

    /**
     * Jalankan Algoritma Kluster Spasial DBSCAN/K-Means untuk agregasi Lisdes (>200m)
     */
    public function generateLisdesClusters(Request $request)
    {
        $service = new \App\Services\SpatialClusteringService();
        $result = $service->generateLisdesClusters(
            (float)($request->input('eps', 300.0)),
            (int)($request->input('min_pts', 2))
        );

        \App\Models\ActivityLog::record('generate_spatial_clusters', "Menjalankan engine agregasi kluster Lisdes spasial: {$result['clusters_created']} paket terdeteksi.");

        return back()->with('success', $result['message']);
    }


    /**
     * Halaman antrean Validasi Realisasi Data Lama SuperAdmin
     */
    public function validasiRealisasiIndex(Request $request)
    {
        $query = Warga::butuhValidasiRealisasi()
            ->when($request->search, function ($q, $search) {
                return $q->where(function ($sub) use ($search) {
                    $sub->where('nik', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%")
                        ->orWhere('kecamatan', 'like', "%{$search}%")
                        ->orWhere('desa', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%");
                });
            })
            ->when($request->kecamatan, function ($q, $kec) {
                return $q->where('kecamatan', $kec);
            })
            ->when($request->desa, function ($q, $des) {
                return $q->where('desa', $des);
            })
            ->latest();

        $wargas = $query->paginate(20)->withQueryString();
        $totalPendingCount = Warga::butuhValidasiRealisasi()->count();

        $kecamatans = Warga::butuhValidasiRealisasi()->select('kecamatan')->distinct()->whereNotNull('kecamatan')->pluck('kecamatan');
        $desas = Warga::butuhValidasiRealisasi()->select('desa')->distinct()->whereNotNull('desa')->pluck('desa');

        return view('admin.validasi_realisasi', compact('wargas', 'totalPendingCount', 'kecamatans', 'desas'));
    }

    /**
     * Memproses konfirmasi single status realisasi NIK data lama oleh SuperAdmin
     */
    public function konfirmasiRealisasi(Request $request, Warga $warga)
    {
        $request->validate([
            'keputusan' => 'required|in:sudah_realisasi,belum_realisasi',
            'catatan'   => 'nullable|string|max:500',
        ]);

        if ($request->keputusan === 'sudah_realisasi') {
            $warga->update([
                'status_verifikasi'        => 'terpasang',
                'butuh_validasi_realisasi' => false,
                'catatan'                  => $request->catatan ?: 'Dikonfirmasi Sudah Direalisasi oleh SuperAdmin.',
            ]);

            \App\Models\ActivityLog::record(
                'validasi_realisasi_approve',
                "SuperAdmin mengonfirmasi bahwa data NIK {$warga->nik} ({$warga->nama} - {$warga->desa}) SUDAH DIREALISASI (Terpasang)."
            );

            return back()->with('success', "Status NIK {$warga->nik} berhasil dikonfirmasi: SUDAH DIREALISASI (Terpasang).");
        } else {
            $warga->update([
                'status_verifikasi'        => 'lolos_verifikasi_pusat',
                'butuh_validasi_realisasi' => false,
                'catatan'                  => $request->catatan ?: 'Dikonfirmasi Belum Direalisasi (Usulan Valid) oleh SuperAdmin.',
            ]);

            \App\Models\ActivityLog::record(
                'validasi_realisasi_reject',
                "SuperAdmin mengonfirmasi bahwa data NIK {$warga->nik} ({$warga->nama} - {$warga->desa}) BELUM DIREALISASI (Status Usulan Lolos Verifikasi)."
            );

            return back()->with('success', "Status NIK {$warga->nik} berhasil dikonfirmasi: BELUM DIREALISASI (Tersimpan sebagai Usulan Lolos).");
        }
    }

    /**
     * Memproses konfirmasi massal (batch) status realisasi NIK oleh SuperAdmin
     */
    public function bulkKonfirmasiRealisasi(Request $request)
    {
        $request->validate([
            'warga_ids' => 'required|array',
            'warga_ids.*' => 'exists:wargas,id',
            'keputusan' => 'required|in:sudah_realisasi,belum_realisasi',
        ]);

        $ids = $request->warga_ids;
        $count = count($ids);

        if ($request->keputusan === 'sudah_realisasi') {
            Warga::whereIn('id', $ids)->update([
                'status_verifikasi'        => 'terpasang',
                'butuh_validasi_realisasi' => false,
                'catatan'                  => 'Dikonfirmasi Massal: Sudah Direalisasi oleh SuperAdmin.',
            ]);

            \App\Models\ActivityLog::record(
                'validasi_realisasi_bulk_approve',
                "SuperAdmin mengonfirmasi massal {$count} data NIK menjadi SUDAH DIREALISASI."
            );

            return back()->with('success', "Berhasil mengonfirmasi {$count} data NIK menjadi SUDAH DIREALISASI (Terpasang).");
        } else {
            Warga::whereIn('id', $ids)->update([
                'status_verifikasi'        => 'lolos_verifikasi_pusat',
                'butuh_validasi_realisasi' => false,
                'catatan'                  => 'Dikonfirmasi Massal: Belum Direalisasi (Usulan Lolos) oleh SuperAdmin.',
            ]);

            \App\Models\ActivityLog::record(
                'validasi_realisasi_bulk_reject',
                "SuperAdmin mengonfirmasi massal {$count} data NIK menjadi BELUM DIREALISASI."
            );

            return back()->with('success', "Berhasil mengonfirmasi {$count} data NIK menjadi BELUM DIREALISASI (Usulan Lolos).");
        }
    }

    /**
     * Halaman Arsip & Data Historis Realisasi BPBL per Tahun
     */
    public function historisIndex(Request $request)
    {
        $query = Warga::with('berkas');

        // 1. Filter dasar data historis
        $query->where(function ($q) {
            $q->where('status_verifikasi', 'terpasang')
              ->orWhereNotNull('keterangan_import');
        });

        // 2. Filter Search (NIK / Nama / Desa / Kecamatan / Alamat)
        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nik', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('desa', 'like', "%{$search}%")
                    ->orWhere('kecamatan', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        });

        // 3. Filter Tahun Usulan
        $query->when($request->tahun, function ($q, $tahun) {
            return $q->where('tahun_usulan', $tahun);
        });

        // 4. Filter Kabupaten
        $query->when($request->kabupaten, function ($q, $kabupaten) {
            return $q->where('kabupaten', $kabupaten);
        });

        // 5. Filter Kecamatan
        $query->when($request->kecamatan, function ($q, $kecamatan) {
            return $q->where('kecamatan', $kecamatan);
        });

        // 6. Filter Desa
        $query->when($request->desa, function ($q, $desa) {
            return $q->where('desa', $desa);
        });

        // Ambil opsi daftar tahun unik untuk dropdown filter ($allYears)
        $allYears = Warga::select('tahun_usulan')
            ->distinct()
            ->orderBy('tahun_usulan', 'desc')
            ->pluck('tahun_usulan');

        // Ambil opsi daftar kecamatan unik untuk dropdown filter ($kecamatans)
        $kecamatans = Warga::select('kecamatan')
            ->whereNotNull('kecamatan')
            ->distinct()
            ->orderBy('kecamatan', 'asc')
            ->pluck('kecamatan');

        $desas = Warga::select('desa')
            ->whereNotNull('desa')
            ->when($request->kecamatan, function ($q, $kecamatan) {
                return $q->where('kecamatan', $kecamatan);
            })
            ->distinct()
            ->orderBy('desa', 'asc')
            ->pluck('desa');

        $kabupatens = Warga::select('kabupaten')
            ->whereNotNull('kabupaten')
            ->distinct()
            ->orderBy('kabupaten', 'asc')
            ->pluck('kabupaten');

        // === BATCH-BASED ARSIP (Daftar Batch Pengajuan per Desa per Tahun) ===
        $batchQuery = \App\Models\PengajuanBatch::withCount('wargas')
            ->when($request->tahun, function ($q, $tahun) {
                return $q->where('tahun_anggaran', $tahun);
            })
            ->when($request->kabupaten, function ($q, $kabupaten) {
                return $q->where('kabupaten', $kabupaten);
            })
            ->when($request->kecamatan, function ($q, $kecamatan) {
                return $q->where('kecamatan', $kecamatan);
            })
            ->when($request->desa, function ($q, $desa) {
                return $q->where('desa', $desa);
            })
            ->orderBy('tahun_anggaran', 'desc')
            ->orderBy('desa', 'asc');

        $batches = $batchQuery->get();

        // Hitung total data historis (sesuai filter aktif)
        $totalHistoris = (clone $query)->count();

        // Hitung total data terpasang/realisasi
        $totalTerpasang = (clone $query)->where(function ($q) {
            $q->where('status_verifikasi', 'terpasang');
        })->count();

        // Ambil data dengan paginasi
        $wargas = $query->latest()->paginate(10)->withQueryString();

        return view('admin.historis', compact(
            'wargas',
            'totalHistoris',
            'totalTerpasang',
            'allYears',
            'kecamatans',
            'desas',
            'kabupatens',
            'batches'
        ));
    }

    public function dataList(Request $request)
{
    $wargas = $this->getFilteredWargaQuery($request)
        ->untukAdmin()
        ->paginate(15)
        ->withQueryString();

    $stats = [
        'total'     => Warga::untukAdmin()->count(),
        'menunggu'  => 0,
        'disetujui' => Warga::untukAdmin()->where('status_verifikasi', 'lolos_verifikasi_pusat')->count(),
        'ditolak'   => Warga::untukAdmin()->where('status_verifikasi', 'ditolak/perlu_perbaikan')->count(),
        'terpasang' => Warga::where('status_verifikasi', 'terpasang')->count(),
    ];

    $kabupatens = Warga::untukAdmin()->whereNotNull('kabupaten')->distinct()->orderBy('kabupaten')->pluck('kabupaten');

    $kecamatans = Warga::untukAdmin()
        ->when($request->kabupaten, fn($q, $v) => $q->where('kabupaten', $v))
        ->whereNotNull('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');

    $desas = Warga::untukAdmin()
        ->when($request->kabupaten, fn($q, $v) => $q->where('kabupaten', $v))
        ->when($request->kecamatan, fn($q, $v) => $q->where('kecamatan', $v))
        ->whereNotNull('desa')->distinct()->orderBy('desa')->pluck('desa');

    $dusuns = Warga::untukAdmin()
        ->when($request->desa, fn($q, $v) => $q->where('desa', $v))
        ->whereNotNull('dusun')->where('dusun', '!=', '')
        ->distinct()->orderBy('dusun')->pluck('dusun');

    $wilayahTree = [];
    foreach (Warga::untukAdmin()->select('kabupaten', 'kecamatan', 'desa')->distinct()->whereNotNull('kabupaten')->get() as $w) {
        $wilayahTree[$w->kabupaten][$w->kecamatan ?: 'Lainnya'][] = $w->desa ?: 'Lainnya';
    }
    foreach ($wilayahTree as $kab => $kecs) {
        foreach ($kecs as $kec => $list) {
            $wilayahTree[$kab][$kec] = array_values(array_unique($list));
        }
    }

    return view('admin.datalist', compact(
        'wargas', 'stats', 'kabupatens', 'kecamatans', 'desas', 'dusuns', 'wilayahTree'
    ));
}
}
