<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;

class ValidasiRealisasiController extends Controller
{
    public function index()
    {
        $data = Warga::butuhValidasiRealisasi()->latest()->paginate(15);
        return view('validasi_realisasi.index', compact('data'));
    }

    public function show(Warga $warga)
    {
        return view('validasi_realisasi.show', compact('warga'));
    }

    public function update(Request $request, Warga $warga)
    {
        $request->validate([
            'status'  => 'required|in:valid,tidak_valid',
            'catatan' => 'nullable|string|max:500',
        ]);

        // sesuaikan nama kolom dengan tabel wargas Anda
        $warga->update([
            'status_validasi_realisasi' => $request->status,
            'catatan_validasi'          => $request->catatan,
        ]);

        return redirect()->route('admin.validasi_realisasi.index')
            ->with('success', 'Validasi tersimpan.');
    }
}
