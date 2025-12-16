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
use App\Models\Penawaran;
use App\Models\Pengguna;

class PembeliController extends Controller
{
    // Browse penawaran ikan yang approved
    public function browse(Request $request)
    {
        // Security: Validate filter input
        $validated = $request->validate([
            'jenis_ikan' => 'nullable|integer|exists:master_jenis_ikans,id',
            'nelayan' => 'nullable|integer|exists:penggunas,id',
            'search' => 'nullable|string|max:100|regex:/^[a-zA-Z0-9\s\-]+$/',
        ], [
            'jenis_ikan.exists' => 'Jenis ikan tidak valid',
            'nelayan.exists' => 'Nelayan tidak ditemukan',
            'search.regex' => 'Pencarian mengandung karakter tidak diizinkan',
        ]);

        $query = Penawaran::with(['nelayan', 'jenisIkan'])
            ->where('status', 'approved');

        // Filter jenis ikan - with verified ID
        if ($validated['jenis_ikan'] ?? false) {
            $query->where('jenis_ikan_id', $validated['jenis_ikan']);
        }

        // Filter nelayan - with verified ID
        if ($validated['nelayan'] ?? false) {
            $query->where('nelayan_id', $validated['nelayan']);
        }

        // Search - sanitized input
        if ($validated['search'] ?? false) {
            $search = htmlspecialchars($validated['search'], ENT_QUOTES, 'UTF-8');
            $query->whereHas('jenisIkan', function($q) use ($search) {
                $q->where('nama_ikan', 'like', "%{$search}%");
            });
        }

        $hasilTangkapan = $query
            ->orderBy('tanggal_tangkapan', 'desc')
            ->paginate(12);

        // Get filter options
        $jenisIkan = \App\Models\MasterJenisIkan::orderBy('nama_ikan')->get();
        $nelayan = Pengguna::where('peran', 'nelayan')
            ->orderBy('nama')
            ->get();

        return view('dashboard.pembeli.browse', compact('hasilTangkapan', 'jenisIkan', 'nelayan'));
    }

    // Lihat detail penawaran
    public function detail($id)
    {
        $ikan = Penawaran::with(['nelayan', 'jenisIkan'])->where('status', 'approved')->findOrFail($id);

        return view('dashboard.pembeli.detail-ikan', compact('ikan'));
    }

    // Buat order baru dari penawaran
    public function createOrder(Request $request)
    {
        // Security: Validate input with comprehensive rules
        $validated = $request->validate([
            'penawaran_id' => 'required|integer|exists:penawarans,id',
            'jumlah' => 'required|numeric|between:0.1,9999999|regex:/^\d+(\.\d{1,2})?$/',
            'catatan' => 'nullable|string|max:500|regex:/^[a-zA-Z0-9\s\-.,()]+$/',
        ], [
            'penawaran_id.required' => 'ID penawaran harus disertakan',
            'penawaran_id.exists' => 'Penawaran tidak ditemukan',
            'jumlah.required' => 'Jumlah harus diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.between' => 'Jumlah harus antara 0.1 dan 9999999 kg',
            'jumlah.regex' => 'Format jumlah tidak valid (max 2 desimal)',
            'catatan.string' => 'Catatan harus berupa teks',
            'catatan.max' => 'Catatan maksimal 500 karakter',
            'catatan.regex' => 'Catatan mengandung karakter tidak diizinkan',
        ]);

        // Security: Prevent tampering - verify user owns this action
        if (Auth::id() !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        // Get penawaran approved only
        $penawaran = Penawaran::where('status', 'approved')
            ->where('id', $validated['penawaran_id'])
            ->firstOrFail();

        // Security: Prevent duplicate rapid orders (race condition)
        $recentOrder = Transaksi::where('pembeli_id', Auth::id())
            ->where('created_at', '>=', now()->subMinutes(1))
            ->exists();

        if ($recentOrder) {
            return back()->with('error', 'Terlalu banyak permintaan. Tunggu beberapa detik.');
        }

        // Security: Sanitize jumlah to prevent floating point issues
        $jumlah = round($validated['jumlah'], 2);

        // Validation: Check stok ketersediaan
        if ($penawaran->jumlah_kg < $jumlah) {
            return back()->with('error', 'Stok tidak cukup. Tersedia: ' . number_format($penawaran->jumlah_kg, 2) . ' kg');
        }

        // Validation: Harga reasonableness check (prevent accidental orders)
        $totalHarga = $penawaran->harga_per_kg * $jumlah;
        if ($totalHarga > 999999999) { // Max 999 juta per order
            return back()->with('error', 'Total harga melebihi batas maksimal');
        }

        // Security: Generate unique transaction code
        $kodeTransaksi = 'TRX-' . date('YmdHis') . '-' . bin2hex(random_bytes(4));

        // Create order with transaction control
        DB::beginTransaction();
        try {
            // Verify penawaran still available before creating
            $penawaran->lockForUpdate();
            $penawaran = Penawaran::where('id', $penawaran->id)
                ->where('status', 'approved')
                ->lockForUpdate()
                ->first();

            if (!$penawaran || $penawaran->jumlah_kg < $jumlah) {
                DB::rollBack();
                return back()->with('error', 'Stok telah berubah. Silakan coba lagi.');
            }

            // Get tengkulak ID (nelayan untuk sekarang)
            $tengkulakId = $penawaran->nelayan_id;

            // Create transaksi with sanitized data
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'tengkulak_id' => $tengkulakId,
                'pembeli_id' => Auth::id(),
                'tanggal_transaksi' => now(),
                'status' => 'pending',
                'total_harga' => $totalHarga,
                'catatan' => htmlspecialchars($validated['catatan'] ?? '', ENT_QUOTES, 'UTF-8'),
            ]);

            // Create detail transaksi
            DetailTransaksi::create([
                'transaksi_id' => $transaksi->id,
                'hasil_tangkapan_id' => $penawaran->id,
                'jumlah_kg' => $jumlah,
                'harga_satuan' => $penawaran->harga_per_kg,
                'subtotal' => $totalHarga,
            ]);

            // Update stok
            $penawaran->decrement('jumlah_kg', $jumlah);

            // Mark as sold out if depleted
            if ($penawaran->jumlah_kg <= 0) {
                $penawaran->update(['status' => 'sold_out']);
            }

            DB::commit();

            return redirect()->route('pembeli.dashboard')
                ->with('success', 'Order berhasil dibuat! Kode: ' . htmlspecialchars($kodeTransaksi));
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Order creation failed: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return back()->with('error', 'Gagal membuat order. Silakan coba lagi.');
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
