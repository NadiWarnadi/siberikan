<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengiriman;
use App\Models\BuktiSerahTerima;

class SopirController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengiriman::with(['transaksi.pembeli', 'transaksi.tengkulak'])
            ->where('sopir_id', Auth::id());

        // Filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_resi', 'like', "%{$search}%")
                  ->orWhere('alamat_tujuan', 'like', "%{$search}%");
            });
        }

        $pengiriman = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('dashboard.sopir', compact('pengiriman'));
    }

    public function updateStatus(Request $request, $id)
    {
        $pengiriman = Pengiriman::where('sopir_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:menunggu,dalam_perjalanan,terkirim,gagal',
            'catatan' => 'nullable|string',
        ]);

        $pengiriman->update($validated);

        if ($validated['status'] === 'terkirim') {
            $pengiriman->update(['tanggal_diterima' => now()]);
        }

        return redirect()->route('sopir.dashboard')
            ->with('success', 'Status pengiriman berhasil diperbarui!');
    }

    public function storeBukti(Request $request, $id)
    {
        $pengiriman = Pengiriman::where('sopir_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        BuktiSerahTerima::create([
            'pengiriman_id' => $pengiriman->id,
            'nama_penerima' => $validated['nama_penerima'],
            'waktu_terima' => now(),
            'catatan' => $validated['catatan'],
        ]);

        $pengiriman->update(['status' => 'terkirim', 'tanggal_diterima' => now()]);

        return redirect()->route('sopir.dashboard')
            ->with('success', 'Bukti serah terima berhasil disimpan!');
    }
}
