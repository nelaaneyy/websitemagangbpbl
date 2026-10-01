<?php

namespace App\Http\Controllers\KepalaDesa;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use App\Models\PengajuanBatch;
use App\Models\ActivityLog;
use App\Models\PengajuanLisdes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KepalaDesaController extends Controller
{
    // List warga di desa yang sama dengan kepala desa yang login (khusus batch aktif tahun berjalan)
    public function index(Request $request)
    {
        $user = $request->user();
        $tahun = (int) date('Y');

        // 1. Cari atau buat Batch Aktif untuk Desa & Tahun berjalan
        $activeBatch = PengajuanBatch::firstOrCreate(
            [
                'desa' => $user->desa,
                'tahun_anggaran' => $tahun,
            ],
            [
                'kode_batch' => 'BPBL-' . $tahun . '-' . Str::slug($user->desa),
                'kecamatan' => $user->kecamatan ?? null,
                'kabupaten' => $user->kabupaten ?? null,
                'created_by_user_id' => $user->id,
                'status' => 'draft_staff',
            ]
        );

        // Update hitungan total warga di batch aktif
        $activeBatch->total_warga = $activeBatch->wargas()->count();
        $activeBatch->save();

        // 2. Query Warga KHUSUS yang masuk ke dalam Batch Aktif Tahun Berjalan (bukan data historis)
        $wargas = Warga::with('berkas')
            ->where('desa', $user->desa)
            ->where(function ($q) use ($activeBatch, $tahun) {
                $q->where('batch_id', $activeBatch->id)
                  ->orWhere(function ($sub) use ($tahun) {
                      $sub->whereNull('batch_id')
                          ->where(function ($t) use ($tahun) {
                              $t->where('tahun_usulan', $tahun)
                                ->orWhereNull('tahun_usulan');
                          })
                          ->whereNotIn('status_verifikasi', ['terpasang', 'realisasi_selesai']);
                  });
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nik', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status_verifikasi', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // 3. Statistik khusus untuk usulan aktif di desa ini
        $baseQuery = Warga::where('desa', $user->desa)
            ->where(function ($q) use ($activeBatch, $tahun) {
                $q->where('batch_id', $activeBatch->id)
                  ->orWhere(function ($sub) use ($tahun) {
                      $sub->whereNull('batch_id')
                          ->where(function ($t) use ($tahun) {
                              $t->where('tahun_usulan', $tahun)
                                ->orWhereNull('tahun_usulan');
                          })
                          ->whereNotIn('status_verifikasi', ['terpasang', 'realisasi_selesai']);
                  });
            });

        $stats = [
            'total'     => (clone $baseQuery)->count(),
            'menunggu'  => (clone $baseQuery)->whereIn('status_verifikasi', ['terkirim', 'pending'])->count(),
            'disetujui' => (clone $baseQuery)->whereIn('status_verifikasi', ['diverifikasi_kades', 'disetujui_desa', 'menunggu_verifikasi_pusat', 'lolos_verifikasi_pusat'])->count(),
            'ditolak'   => (clone $baseQuery)->where('status_verifikasi', 'ditolak/perlu_perbaikan')->count(),
        ];

        return view('kepaladesa.index', compact('wargas', 'stats', 'activeBatch'));
    }

     /* Kepala Desa mengesahkan SPTJM dan meneruskan Batch ke Dinas ESDM
     */
    public function submitBatchToEsdm(Request $request, PengajuanBatch $batch)
    {
        if (mb_strtolower(trim($batch->desa)) !== mb_strtolower(trim($request->user()->desa))) {
            abort(403, 'Anda hanya dapat mengelola data usulan di desa Anda.');
        }

        $request->validate([
            'sptjm_agreement'       => 'accepted',
            'nomor_surat_pengantar' => 'nullable|string|max:100',
            'catatan_kades'         => 'nullable|string|max:1000',
        ], [
            'sptjm_agreement.accepted' => 'Anda wajib menyetujui pernyataan keabsahan dan legalitas dokumen (SPTJM) sebelum mengirim berkas ke Dinas ESDM.',
        ]);

        $batch->update([
            'status'                => 'diajukan_ke_esdm',
            'sptjm_accepted_at'     => now(),
            'kades_id'              => $request->user()->id,
            'nomor_surat_pengantar' => $request->nomor_surat_pengantar ?: ('B-500.10.17/' . date('Y') . '/' . strtoupper(Str::slug($batch->desa))),
            'catatan_kades'         => $request->catatan_kades,
            'total_warga'           => $batch->wargas()->count(),
        ]);

        // Teruskan semua warga di batch ke verifikasi pusat (ESDM)
        $batch->wargas()->whereIn('status_verifikasi', ['terkirim', 'pending', 'diverifikasi_kades', 'disetujui_desa'])->update([
            'status_verifikasi' => 'menunggu_verifikasi_pusat',
        ]);

        ActivityLog::record(
            'kades_submit_batch_esdm',
            'Kepala Desa ' . $batch->desa . ' resmi mengesahkan SPTJM dan meneruskan Batch Usulan ' . $batch->kode_batch . ' (' . $batch->total_warga . ' warga) ke Dinas ESDM.'
        );

        return back()->with('success', 'Batch Usulan ' . $batch->kode_batch . ' beserta Surat Pernyataan Tanggung Jawab Mutlak (SPTJM) telah resmi dikirim ke Dinas ESDM!');
    }

    public function show(Warga $warga)
    {
        $this->authorizeDesa($warga);
        $warga->load('berkas');
        return view('kepaladesa.show', compact('warga'));
    }

    // Approve -> lanjut ke instansi
    public function approve(Warga $warga)
    {
        $this->authorizeDesa($warga);

        $warga->update(['status_verifikasi' => 'diverifikasi_kades']);

        ActivityLog::record(
            'kades_approve',
            'Kepala Desa memverifikasi kelayakan warga NIK ' . $warga->nik . ' (' . $warga->nama . ' - Desil: ' . ($warga->desil ?? '-') . ')'
        );

        return back()->with('success', 'Berkas warga telah divalidasi layak oleh Kepala Desa.');
    }

    // Reject -> minta perbaikan
    public function reject(Request $request, Warga $warga)
    {
        $this->authorizeDesa($warga);

        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        $warga->update([
            'status_verifikasi' => 'ditolak/perlu_perbaikan',
            'catatan'           => $request->catatan,
            'ditolak_oleh'      => 'kades',
        ]);

        ActivityLog::record(
            'kades_reject',
            'Kepala Desa menolak berkas warga NIK ' . $warga->nik . ' dengan catatan: ' . ($request->catatan ?: 'Tanpa catatan')
        );

        return back()->with('success', 'Berkas warga ditolak, menunggu perbaikan.');
    }

    // Edit data warga (opsional koreksi data)
    public function update(Request $request, Warga $warga)
    {
        $this->authorizeDesa($warga);

        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'rt_rw'     => 'required|string|max:10',
            'alamat'    => 'required|string',
        ]);

        $warga->update($validated);

        return back()->with('success', 'Data warga berhasil diperbarui.');
    }

    // Hapus pengajuan (mis. data ganda/salah input)
    public function destroy(Warga $warga)
    {
        $this->authorizeDesa($warga);
        $warga->delete();

        return redirect()->route('kepaladesa.index')->with('success', 'Data warga berhasil dihapus.');
    }

    private function authorizeDesa(Warga $warga): void
    {
        if (mb_strtolower(trim($warga->desa)) !== mb_strtolower(trim(request()->user()->desa))) {
            abort(403, 'Anda hanya dapat mengelola data warga di desa Anda.');
        }
    }

    public function lisdesIndex(Request $request)
    {
        $lisdesList = PengajuanLisdes::where('desa', $request->user()->desa)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total'               => PengajuanLisdes::where('desa', $request->user()->desa)->count(),
            'menunggu_verifikasi' => PengajuanLisdes::where('desa', $request->user()->desa)->where('status', 'menunggu_verifikasi')->count(),
            'disetujui'           => PengajuanLisdes::where('desa', $request->user()->desa)->where('status', 'disetujui')->count(),
            'ditolak'             => PengajuanLisdes::where('desa', $request->user()->desa)->where('status', 'ditolak')->count(),
        ];

        return view('kepaladesa.lisdes.index', compact('lisdesList', 'stats'));
    }

    public function createLisdes()
    {
        return view('kepaladesa.lisdes.create');
    }

    public function editLisdes(PengajuanLisdes $lisdes)
{
    $this->authorizeKades($lisdes);
    return view('kepaladesa.lisdes.edit', compact('lisdes'));
}

public function updateLisdes(Request $request, PengajuanLisdes $lisdes)
{
    $this->authorizeKades($lisdes);

    $validated = $request->validate([
        'nama_dusun'         => 'required|string|max:255',
        'jumlah_kk'          => 'required|integer|min:1',
        'estimasi_jarak'     => 'required|integer|min:0',
        'keterangan_wilayah' => 'nullable|string',
        'latitude'           => 'required|numeric|between:-90,90',
        'longitude'          => 'required|numeric|between:-180,180',
        'topology_data'      => 'nullable|string',
        'surat_permohonan'   => 'nullable|file|mimes:pdf|max:10240',
        'proposal_lisdes'    => 'nullable|file|mimes:pdf|max:10240',
        'foto_wilayah'       => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:5120',
    ]);

    if ($request->hasFile('surat_permohonan')) {
        $validated['surat_permohonan'] = $request->file('surat_permohonan')->store('pengajuan_lisdes/surat', 'public');
    }
    if ($request->hasFile('proposal_lisdes')) {
        $validated['proposal_lisdes'] = $request->file('proposal_lisdes')->store('pengajuan_lisdes/proposal', 'public');
    }
    if ($request->hasFile('foto_wilayah')) {
        $validated['foto_wilayah'] = $request->file('foto_wilayah')->store('pengajuan_lisdes/foto', 'public');
    }

    $lisdes->update($validated);

    return redirect()->route('kepaladesa.lisdes.index')->with('success', 'Pengajuan Lisdes berhasil diperbarui.');
}

private function authorizeKades(PengajuanLisdes $lisdes): void
{
    if (mb_strtolower(trim($lisdes->desa)) !== mb_strtolower(trim(request()->user()->desa))) {
        abort(403, 'Anda hanya dapat mengelola data lisdes di desa Anda.');
    }
}

    public function storeLisdes(Request $request)
    {
        $validated = $request->validate([
            'nama_dusun'         => 'required|string|max:255',
            'jumlah_kk'          => 'required|integer|min:1',
            'estimasi_jarak'     => 'required|integer|min:0',
            'keterangan_wilayah' => 'nullable|string',
            'surat_permohonan'   => 'required|file|mimes:pdf|max:10240',
            'proposal_lisdes'    => 'required|file|mimes:pdf|max:10240',
            'foto_wilayah'       => 'required|file|image|mimes:jpeg,png,jpg,webp|max:5120',
            'latitude'           => 'required|numeric|between:-90,90',
            'longitude'          => 'required|numeric|between:-180,180',
            'topology_data'      => 'nullable|string',
        ]);

        $suratPath = $request->file('surat_permohonan')->store('pengajuan_lisdes/surat', 'public');
        $proposalPath = $request->file('proposal_lisdes')->store('pengajuan_lisdes/proposal', 'public');
        $fotoPath = $request->file('foto_wilayah')->store('pengajuan_lisdes/foto', 'public');

        PengajuanLisdes::create([
            'user_id'            => $request->user()->id,
            'desa'               => $request->user()->desa,
            'nama_dusun'         => $validated['nama_dusun'],
            'jumlah_kk'          => $validated['jumlah_kk'],
            'estimasi_jarak'     => $validated['estimasi_jarak'],
            'keterangan_wilayah' => $validated['keterangan_wilayah'] ?? null,
            'surat_permohonan'   => $suratPath,
            'proposal_lisdes'    => $proposalPath,
            'foto_wilayah'       => $fotoPath,
            'latitude'           => $validated['latitude'],
            'longitude'          => $validated['longitude'],
            'topology_data'      => $request->topology_data ?? null,
            'status'             => 'menunggu_verifikasi',
        ]);

        return redirect()->route('kepaladesa.lisdes.index')->with('success', 'Usulan Lisdes Dusun berhasil diajukan ke Dinas ESDM!');
    }
}
