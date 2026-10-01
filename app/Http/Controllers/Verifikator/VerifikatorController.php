<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warga;
use App\Models\User;
use App\Models\AuditLog;

class VerifikatorController extends Controller
{
    private function getFilteredWargaQuery(Request $request)
    {
        return Warga::with('berkas')
            ->when($request->status, function ($query, $status) {
                if ($status === 'butuh_validasi') {
                    return $query->where('butuh_validasi_realisasi', true);
                }
                return $query->where('status_verifikasi', $status);
            })
            ->when($request->kabupaten, fn($q, $v) => $q->where('kabupaten', $v))
            ->when($request->kecamatan, fn($q, $v) => $q->where('kecamatan', $v))
            ->when($request->desa, function ($query, $desa) {
                if (is_array($desa)) {
                    $filtered = array_filter($desa);
                    return !empty($filtered) ? $query->whereIn('desa', $filtered) : $query;
                }
                return $query->where('desa', $desa);
            })
            ->when($request->dusun, fn($q, $v) => $q->where('dusun', $v))
            ->when($request->tahun, function ($query, $tahun) {
                return $query->where(function($q) use ($tahun) {
                    $q->where('tahun_usulan', $tahun)->orWhereYear('created_at', $tahun);
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

    private function getWilayahData(Request $request): array
    {
        $kabupatens = Warga::select('kabupaten')->distinct()->whereNotNull('kabupaten')->orderBy('kabupaten')->pluck('kabupaten');

        $kecamatans = Warga::when($request->kabupaten, fn($q, $v) => $q->where('kabupaten', $v))
            ->select('kecamatan')->distinct()->whereNotNull('kecamatan')->orderBy('kecamatan')->pluck('kecamatan');

        $desas = Warga::when($request->kabupaten, fn($q, $v) => $q->where('kabupaten', $v))
            ->when($request->kecamatan, fn($q, $v) => $q->where('kecamatan', $v))
            ->select('desa')->distinct()->whereNotNull('desa')->orderBy('desa')->pluck('desa');

        $dusuns = Warga::when($request->kabupaten, fn($q, $v) => $q->where('kabupaten', $v))
            ->when($request->kecamatan, fn($q, $v) => $q->where('kecamatan', $v))
            ->when($request->desa, fn($q, $v) => $q->where('desa', $v))
            ->select('dusun')->distinct()->whereNotNull('dusun')->where('dusun', '!=', '')->orderBy('dusun')->pluck('dusun');

        $wilayahRecords = Warga::select('kabupaten', 'kecamatan', 'desa')->distinct()->whereNotNull('kabupaten')->get();
        $wilayahTree = [];
        foreach ($wilayahRecords as $w) {
            $kab = $w->kabupaten;
            $kec = $w->kecamatan ?: 'Lainnya';
            $des = $w->desa ?: 'Lainnya';
            $wilayahTree[$kab][$kec] = $wilayahTree[$kab][$kec] ?? [];
            if (!in_array($des, $wilayahTree[$kab][$kec])) {
                $wilayahTree[$kab][$kec][] = $des;
            }
        }

        return compact('kabupatens', 'kecamatans', 'desas', 'dusuns', 'wilayahTree');
    }

    public function index(Request $request)
    {
        $wargas = $this->getFilteredWargaQuery($request)->paginate(15)->withQueryString();

        $stats = [
            'total'     => Warga::count(),
            'menunggu'  => Warga::whereIn('status_verifikasi', ['menunggu_verifikasi_pusat', 'disetujui_desa', 'terkirim', 'pending'])->count(),
            'disetujui' => Warga::whereIn('status_verifikasi', ['lolos_verifikasi_pusat', 'terpasang'])->count(),
            'ditolak'   => Warga::where('status_verifikasi', 'ditolak/perlu_perbaikan')->count(),
            'terpasang' => Warga::where('status_verifikasi', 'terpasang')->count(),
        ];

        $chartKabupaten = Warga::select('kabupaten', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereNotNull('kabupaten')
            ->groupBy('kabupaten')
            ->pluck('total', 'kabupaten');

        $chartStatus = [
            'terkirim'               => Warga::where('status_verifikasi', 'terkirim')->count(),
            'disetujui_desa'         => Warga::where('status_verifikasi', 'disetujui_desa')->count(),
            'lolos_verifikasi_pusat' => Warga::where('status_verifikasi', 'lolos_verifikasi_pusat')->count(),
            'ditolak'                => Warga::where('status_verifikasi', 'ditolak/perlu_perbaikan')->count(),
        ];

        return view('verifikator.index', array_merge(
            compact('wargas', 'stats', 'chartKabupaten', 'chartStatus'),
            $this->getWilayahData($request)
        ));
    }

    public function dataList(Request $request)
    {
        $wargas = $this->getFilteredWargaQuery($request)
        ->aktif()
        ->paginate(15)
        ->withQueryString();

    $stats = [
        'total'     => Warga::aktif()->count(),
        'menunggu'  => Warga::aktif()->whereIn('status_verifikasi', ['menunggu_verifikasi_pusat', 'disetujui_desa', 'terkirim', 'pending'])->count(),
        'disetujui' => Warga::aktif()->whereIn('status_verifikasi', ['lolos_verifikasi_pusat'])->count(),
        'ditolak'   => Warga::aktif()->where('status_verifikasi', 'ditolak/perlu_perbaikan')->count(),
        'terpasang' => Warga::where('status_verifikasi', 'terpasang')->count(),
    ];

        return view('admin.datalist', array_merge(
            compact('wargas', 'stats'),
            $this->getWilayahData($request)
        ));
    }

    public function show(Warga $warga)
    {
        $warga->load('berkas');
        return view('verifikator.show', compact('warga'));
    }

    public function approve(Warga $warga, Request $request)
    {
        $warga->update(['status_verifikasi' => 'lolos_verifikasi_pusat']);

        \App\Models\ActivityLog::record(
            'esdm_approve',
            'Verifikator menyetujui pengajuan warga NIK ' . $warga->nik . ' (' . $warga->nama . ' - ' . $warga->desa . ')'
        );

        $request->validate(['catatan' => 'nullable|string|max:500']);
        $warga->status_verifikasi = 'lolos_verifikasi_pusat';
        if ($request->filled('catatan')) $warga->catatan = $request->catatan;
        $warga->save();
        AuditLog::record('APPROVE_WARGA', "Verifikator meloloskan warga #{$warga->id}");

        return back()->with('success', 'Warga lolos verifikasi pusat.');
    }

    public function reject(Request $request, Warga $warga)
    {
        $request->validate(['catatan' => 'nullable|string|max:500']);

        $warga->update([
            'status_verifikasi' => 'ditolak/perlu_perbaikan',
            'catatan'           => $request->catatan,
            'ditolak_oleh'      => 'verifikator',
        ]);

        \App\Models\ActivityLog::record(
            'esdm_reject',
            'Verifikator menolak pengajuan warga NIK ' . $warga->nik . ' dengan catatan: ' . ($request->catatan ?: 'Tanpa catatan')
        );

        return back()->with('success', 'Berkas warga ditolak.');
    }

    public function destroy(Warga $warga)
    {
        $warga->delete();
        return redirect()->route('verifikator.index')->with('success', 'Data warga berhasil dihapus.');
    }

    public function addCatatan(Request $request, Warga $warga)
    {
        $request->validate(['catatan_verifikator' => 'required|string|max:1000']);

        $warga->update(['catatan_verifikator' => $request->catatan_verifikator]);

        \App\Models\ActivityLog::record(
            'verifikator_catatan',
            'Verifikator menambahkan catatan pada NIK ' . $warga->nik . ': ' . $request->catatan_verifikator
        );

        return back()->with('success', 'Catatan berhasil ditambahkan.');
    }

    public function users(Request $request)
    {
        $status = in_array($request->query('status'), ['pending', 'approved', 'rejected'])
            ? $request->query('status') : 'pending';

        $roleFilter = $request->query('role', 'all');

        $query = User::whereIn('role', ['kepala_desa', 'staff_desa', 'staf_desa']);
        if (in_array($roleFilter, ['kepala_desa', 'staff_desa', 'staf_desa'])) {
            $query->where('role', $roleFilter);
        }

        $countPending  = (clone $query)->where('status', 'pending')->count();
        $countApproved = (clone $query)->where('status', 'approved')->count();
        $countRejected = (clone $query)->where('status', 'rejected')->count();

        $users = (clone $query)->where('status', $status)->latest()->paginate(15)->withQueryString();

        return view('verifikator.users.index', compact(
            'users', 'status', 'roleFilter',
            'countPending', 'countApproved', 'countRejected'
        ));
    }

    public function approveUser(User $user)
    {
        $user->update(['status' => 'approved']);
        return back()->with('success', 'Akun ' . $user->name . ' (' . ucfirst($user->role) . ') berhasil disetujui.');
    }

    public function rejectUser(User $user)
    {
        $user->update(['status' => 'rejected']);
        return back()->with('success', 'Pendaftaran akun ' . $user->name . ' ditolak.');
    }

    public function downloadBavlPdf(Warga $warga)
    {
        $service = new \App\Services\BavlGeneratorService();
        $pdf = $service->generateBavlPdf($warga);
        return $pdf->download("BAVL_Verifikasi_ESDM_{$warga->nik}.pdf");
    }

    public function downloadLisdesMapPdf($id, Request $request)
    {
        $type    = $request->query('type', 'lisdes');
        $service = new \App\Services\LisdesMapPdfService();
        $pdf     = $service->generateMapPdf($id, $type);
        return $pdf->download("Peta_Layout_Kartografi_Lisdes_{$id}.pdf");
    }

    public function exportPdf(Request $request)
    {
        return app(\App\Http\Controllers\Admin\AdminController::class)->exportPdf($request);
    }

    public function exportExcel(Request $request)
    {
        return app(\App\Http\Controllers\Admin\AdminController::class)->exportExcel($request);
    }
}
