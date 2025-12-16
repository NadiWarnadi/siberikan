<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use App\Models\Pengiriman;
use App\Models\ReturBarang;
use App\Models\HasilTangkapan;
use App\Models\DetailTransaksi;
use App\Models\Pengguna;

class PembeliController extends Controller
{
    // Browse ikan yang tersedia
    public function browse(Request $request)
    {
        $query = HasilTangkapan::with(['nelayan', 'jenisIkan'])
            ->where('status', 'tersedia');

        // Filter by jenis ikan
        if ($request->has('jenis_ikan') && $request->jenis_ikan != '') {
            $query->where('jenis_ikan_id', $request->jenis_ikan);
        }

        // Filter by nelayan
        if ($request->has('nelayan') && $request->nelayan != '') {
            $query->where('nelayan_id', $request->nelayan);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('jenisIkan', function($q) use ($search) {
                $q->where('nama_ikan', 'like', "%{$search}%");
            });
        }

        $hasilTangkapan = $query->orderBy('tanggal_tangkapan', 'desc')->paginate(12);
        
        // Get jenis ikan for filter
        $jenisIkan = \App\Models\MasterJenisIkan::all();
        
        // Get nelayan for filter
        $nelayan = Pengguna::where('peran', 'nelayan')->get();

        return view('dashboard.pembeli.browse', compact('hasilTangkapan', 'jenisIkan', 'nelayan'));
    }

    // Lihat detail ikan
    public function detail($id)
    {
        $ikan = HasilTangkapan::with(['nelayan', 'jenisIkan'])->findOrFail($id);

        return view('dashboard.pembeli.detail-ikan', compact('ikan'));
    }

    // Buat order baru
    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'hasil_tangkapan_id' => 'required|exists:hasil_tangkapan,id',
            'jumlah' => 'required|numeric|min:1',
            'catatan' => 'nullable|string',
        ]);

        // Get hasil tangkapan
        $hasilTangkapan = HasilTangkapan::findOrFail($validated['hasil_tangkapan_id']);

        // Cek stok
        if ($hasilTangkapan->jumlah_kg < $validated['jumlah']) {
            return back()->with('error', 'Stok tidak cukup. Tersedia: ' . $hasilTangkapan->jumlah_kg . ' kg');
        }

        // Generate kode transaksi
        $kodeTransaksi = 'TRX-' . date('YmdHis') . '-' . rand(1000, 9999);

        // Create transaksi
        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'nelayan_id' => $hasilTangkapan->nelayan_id,
                'pembeli_id' => Auth::id(),
                'tanggal_transaksi' => now(),
                'status' => 'pending',
                'total_harga' => $hasilTangkapan->harga_per_kg * $validated['jumlah'],
                'catatan' => $validated['catatan'] ?? '',
            ]);

            // Create detail transaksi
            DetailTransaksi::create([
                'transaksi_id' => $transaksi->id,
                'hasil_tangkapan_id' => $hasilTangkapan->id,
                'jumlah_kg' => $validated['jumlah'],
                'harga_satuan' => $hasilTangkapan->harga_per_kg,
                'subtotal' => $hasilTangkapan->harga_per_kg * $validated['jumlah'],
            ]);

            // Update stok hasil tangkapan
            $hasilTangkapan->decrement('jumlah_kg', $validated['jumlah']);

            // Jika stok habis, ubah status
            if ($hasilTangkapan->jumlah_kg <= 0) {
                $hasilTangkapan->update(['status' => 'habis']);
            }

            // Generate surat pengiriman otomatis
            $pengiriman = \App\Http\Controllers\SuratPengirimanController::generateSuratOtomatis($transaksi->id);

            DB::commit();

            return redirect()->route('pembeli.dashboard')
                ->with('success', 'Order berhasil dibuat! Kode transaksi: ' . $kodeTransaksi . ' | Nomor Resi: ' . $pengiriman->nomor_resi);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat order: ' . $e->getMessage());
        }
    }

    // Dashboard pembeli
    public function index(Request $request)
    {
        // Purchase history
        $query = Transaksi::with(['nelayan', 'details.hasilTangkapan.jenisIkan', 'pengiriman'])
            ->where('pembeli_id', Auth::id());

        // Filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('kode_transaksi', 'like', "%{$search}%");
        }

        $transaksi = $query->orderBy('created_at', 'desc')->paginate(10);

        // Returns
        $retur = ReturBarang::with('transaksi')
            ->where('pembeli_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.pembeli', compact('transaksi', 'retur'));
    }

    public function confirmReceipt($id)
    {
        $pengiriman = Pengiriman::whereHas('transaksi', function($q) {
            $q->where('pembeli_id', Auth::id());
        })->findOrFail($id);

        $pengiriman->update([
            'status' => 'terkirim',
            'tanggal_diterima' => now(),
        ]);

        $pengiriman->transaksi->update(['status' => 'selesai']);

        return redirect()->route('pembeli.dashboard')
            ->with('success', 'Penerimaan barang berhasil dikonfirmasi!');
    }

    public function submitRetur(Request $request)
    {
        $validated = $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'alasan' => 'required|in:rusak,tidak_sesuai,kadaluarsa,lainnya',
            'keterangan' => 'required|string',
            'jumlah_pengembalian' => 'required|numeric|min:0',
        ]);

        // Check if transaksi belongs to this user
        $transaksi = Transaksi::where('pembeli_id', Auth::id())
            ->findOrFail($validated['transaksi_id']);

        ReturBarang::create([
            'transaksi_id' => $validated['transaksi_id'],
            'pembeli_id' => Auth::id(),
            'tanggal_retur' => now(),
            'alasan' => $validated['alasan'],
            'keterangan' => $validated['keterangan'],
            'jumlah_pengembalian' => $validated['jumlah_pengembalian'],
            'status' => 'diajukan',
        ]);

        return redirect()->route('pembeli.dashboard')
            ->with('success', 'Pengajuan retur berhasil dikirim!');
    }
}
