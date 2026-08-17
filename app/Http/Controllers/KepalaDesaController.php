<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;

class KepalaDesaController extends Controller
{
    // List warga di desa yang sama dengan kepala desa yang login
    public function index(Request $request)
    {
        $wargas = Warga::with('berkas')
            ->where('desa', $request->user()->desa)
            ->when($request->search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
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

        $stats = [
            'total' => Warga::where('desa', $request->user()->desa)->count(),
            'menunggu' => Warga::where('desa', $request->user()->desa)->whereIn('status_verifikasi', ['terkirim', 'pending'])->count(),
            'disetujui' => Warga::where('desa', $request->user()->desa)->whereIn('status_verifikasi', ['menunggu_verifikasi_pusat', 'lolos_verifikasi_pusat'])->count(),
            'ditolak' => Warga::where('desa', $request->user()->desa)->where('status_verifikasi', 'ditolak/perlu_perbaikan')->count(),
        ];

        return view('kepaladesa.index', compact('wargas', 'stats'));
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

        $warga->update(['status_verifikasi' => 'disetujui_desa']);

        \App\Models\ActivityLog::record(
            'kades_approve',
            'Kepala Desa menyetujui pengajuan warga NIK ' . $warga->nik . ' (' . $warga->nama . ')'
        );

        return back()->with('success', 'Berkas warga telah diverifikasi dan diteruskan ke instansi.');
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

        \App\Models\ActivityLog::record(
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

    public function createLisdes()
    {
        return view('kepaladesa.pengajuanlisdes');
    }

    public function storeLisdes(Request $request)
    {
        $validated = $request->validate([
            'nama_dusun'         => 'required|string|max:255',
            'jumlah_kk'          => 'required|integer|min:1',
            'estimasi_jarak'     => 'required|integer|min:1',
            'keterangan_wilayah' => 'nullable|string',
            'surat_permohonan'   => 'required|file|mimes:pdf|max:10240',
            'proposal_lisdes'    => 'required|file|mimes:pdf|max:10240',
            'foto_wilayah'       => 'required|file|image|mimes:jpeg,png,jpg,webp|max:5120',
            'latitude'           => 'required|numeric|between:-90,90',
            'longitude'          => 'required|numeric|between:-180,180',
        ]);

        $suratPath = $request->file('surat_permohonan')->store('pengajuan_lisdes/surat', 'public');
        $proposalPath = $request->file('proposal_lisdes')->store('pengajuan_lisdes/proposal', 'public');
        $fotoPath = $request->file('foto_wilayah')->store('pengajuan_lisdes/foto', 'public');

        \App\Models\PengajuanLisdes::create([
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
            'status'             => 'menunggu_verifikasi',
        ]);

        return redirect()->route('kepaladesa.index')->with('success', 'Usulan Lisdes berhasil dikirim ke Dinas ESDM!');
    }
}
