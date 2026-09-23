<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\Warga;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = WorkOrder::with(['warga', 'vendor', 'petugas']);

        // Vendor PLN hanya melihat WO yang ditugaskan kepadanya
        if ($user->isPlnVendor()) {
            $query->where('vendor_id', $user->id);
        }

        $workOrders = $query->latest()->paginate(15)->withQueryString();
        $vendors = User::where('role', 'pln_vendor')->get();
        $petugas = User::where('role', 'petugas_lapangan')->get();

        return view('dinasesdm.work_orders.index', compact('workOrders', 'vendors', 'petugas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warga_id'  => 'required|exists:wargas,id',
            'vendor_id' => 'nullable|exists:users,id',
            'tanggal_wo' => 'required|date',
            'catatan_verifikator' => 'nullable|string',
        ]);

        $warga = Warga::findOrFail($validated['warga_id']);

        $wo = WorkOrder::create([
            'warga_id' => $warga->id,
            'vendor_id' => $validated['vendor_id'],
            'nomor_wo' => 'WO-PLN/' . date('Ym') . '/' . sprintf('%04d', rand(100, 9999)),
            'tanggal_wo' => $validated['tanggal_wo'],
            'status' => 'ditugaskan',
            'catatan_verifikator' => $validated['catatan_verifikator'],
        ]);

        // State Transition
        $warga->update(['status_verifikasi' => 'proses_pemasangan']);

        ActivityLog::record(
            'create_work_order',
            "Menerbitkan Work Order Pemasangan {$wo->nomor_wo} untuk NIK {$warga->nik} ({$warga->nama})",
            null,
            "{$warga->latitude},{$warga->longitude}",
            ['wo_id' => $wo->id, 'status' => 'proses_pemasangan']
        );

        return back()->with('success', "Work Order {$wo->nomor_wo} berhasil dibuat!");
    }

    public function updateStatus(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:proses_pemasangan,selesai_pemasangan,terverifikasi',
            'nomor_kwh_meter' => 'nullable|string',
            'foto_pemasangan' => 'nullable|image|max:2048',
            'foto_kwh_terpasang' => 'nullable|image|max:2048',
            'catatan_vendor' => 'nullable|string',
        ]);

        $data = [
            'status' => $validated['status'],
            'nomor_kwh_meter' => $validated['nomor_kwh_meter'] ?? $workOrder->nomor_kwh_meter,
            'catatan_vendor' => $validated['catatan_vendor'] ?? $workOrder->catatan_vendor,
        ];

        if ($request->hasFile('foto_pemasangan')) {
            if ($workOrder->foto_pemasangan) {
                Storage::disk('public')->delete($workOrder->foto_pemasangan);
            }
            $data['foto_pemasangan'] = $request->file('foto_pemasangan')->store('work_orders/pemasangan', 'public');
        }

        if ($request->hasFile('foto_kwh_terpasang')) {
            if ($workOrder->foto_kwh_terpasang) {
                Storage::disk('public')->delete($workOrder->foto_kwh_terpasang);
            }
            $data['foto_kwh_terpasang'] = $request->file('foto_kwh_terpasang')->store('work_orders/kwh', 'public');
        }

        if ($validated['status'] === 'selesai_pemasangan') {
            $data['tanggal_pasang'] = now();
        }

        $workOrder->update($data);

        // Jika disetujui / terverifikasi final oleh ESDM -> status_verifikasi warga = 'terpasang'
        if ($validated['status'] === 'terverifikasi') {
            $workOrder->warga->update(['status_verifikasi' => 'terpasang']);
        }

        ActivityLog::record(
            'update_work_order',
            "Pembaruan status Work Order {$workOrder->nomor_wo} menjadi: {$validated['status']}",
            null,
            "{$workOrder->warga->latitude},{$workOrder->warga->longitude}",
            ['wo_id' => $workOrder->id, 'status' => $validated['status']]
        );

        return back()->with('success', "Status Work Order {$workOrder->nomor_wo} berhasil diperbarui.");
    }
}
