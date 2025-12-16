<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pengiriman;
use App\Models\Transaksi;
use App\Models\Pengguna;
use App\Models\BuktiSerahTerima;

class SuratPengirimanController extends Controller
{
    /**
     * Generate surat pengiriman otomatis ketika order dibuat
     * Dipanggil otomatis dari PembeliController::createOrder
     */
    public static function generateSuratOtomatis($transaksiId)
    {
        try {
            $transaksi = Transaksi::findOrFail($transaksiId);
            
            // Generate nomor resi
            $nomorResi = 'SRT-' . date('YmdHis') . '-' . rand(1000, 9999);
            
            // Ambil sopir yang tersedia (random untuk demo)
            // Dalam production, ini bisa menggunakan algoritma yang lebih kompleks
            $sopirTersedia = Pengguna::where('peran', 'sopir')
                ->whereDoesntHave('pengiriman', function($q) {
                    $q->whereIn('status', ['pending', 'dikirim']);
                })
                ->first();

            // Jika tidak ada sopir yang fully available, ambil yang punya pengiriman terbanyak
            if (!$sopirTersedia) {
                $sopirTersedia = Pengguna::where('peran', 'sopir')
                    ->withCount('pengiriman')
                    ->orderBy('pengiriman_count', 'asc')
                    ->first();
            }

            if (!$sopirTersedia) {
                throw new \Exception('Tidak ada sopir yang tersedia');
            }

            // Ambil alamat tujuan dari pembeli
            $alamatTujuan = $transaksi->pembeli->alamat ?? 'Tidak diketahui';
            
            // Estimasi pengiriman 1-3 hari kerja
            $estimasiKirim = now()->addDays(rand(1, 3));

            // Create pengiriman
            $pengiriman = Pengiriman::create([
                'transaksi_id' => $transaksiId,
                'sopir_id' => $sopirTersedia->id,
                'nomor_resi' => $nomorResi,
                'tanggal_kirim' => now(),
                'tanggal_estimasi' => $estimasiKirim,
                'alamat_tujuan' => $alamatTujuan,
                'status' => 'pending',
                'catatan' => $transaksi->catatan ?? '',
            ]);

            // Update status transaksi menjadi dikemas
            $transaksi->update(['status' => 'dikemas']);

            // Log activity
            \Log::info('Surat pengiriman berhasil dibuat', [
                'pengiriman_id' => $pengiriman->id,
                'nomor_resi' => $nomorResi,
                'transaksi_id' => $transaksiId,
                'sopir_id' => $sopirTersedia->id,
            ]);

            return $pengiriman;
        } catch (\Exception $e) {
            \Log::error('Gagal membuat surat pengiriman: ' . $e->getMessage(), [
                'transaksi_id' => $transaksiId
            ]);
            throw $e;
        }
    }

    /**
     * Lihat detail surat pengiriman untuk sopir
     */
    public function viewSurat($nomorResi)
    {
        $pengiriman = Pengiriman::with([
            'transaksi.pembeli',
            'transaksi.nelayan',
            'transaksi.details.hasilTangkapan.jenisIkan',
            'sopir'
        ])->where('nomor_resi', $nomorResi)->firstOrFail();

        // Check if user is the assigned sopir or admin
        if (Auth::user()->peran != 'admin' && $pengiriman->sopir_id != Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke surat ini');
        }

        return view('dashboard.sopir.surat-pengiriman', compact('pengiriman'));
    }

    /**
     * Download surat pengiriman sebagai PDF
     */
    public function downloadSurat($nomorResi)
    {
        $pengiriman = Pengiriman::with([
            'transaksi.pembeli',
            'transaksi.nelayan',
            'transaksi.details.hasilTangkapan.jenisIkan',
            'sopir'
        ])->where('nomor_resi', $nomorResi)->firstOrFail();

        // Check if user is the assigned sopir
        if ($pengiriman->sopir_id != Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke surat ini');
        }

        $html = view('exports.surat-pengiriman-pdf', compact('pengiriman'))->render();

        // Simpan HTML ke PDF menggunakan library (jika ada)
        // Untuk saat ini, return view yang bisa di-print
        return view('exports.surat-pengiriman-print', compact('pengiriman'));
    }

    /**
     * Update status pengiriman
     */
    public function updateStatus(Request $request, $id)
    {
        $pengiriman = Pengiriman::findOrFail($id);

        // Check if user is the assigned sopir
        if ($pengiriman->sopir_id != Auth::id()) {
            abort(403, 'Anda tidak memiliki akses');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,dikirim,terkirim',
        ]);

        $pengiriman->update(['status' => $validated['status']]);

        // Jika sudah terkirim, update transaksi
        if ($validated['status'] == 'terkirim') {
            $pengiriman->transaksi->update(['status' => 'selesai']);
            $pengiriman->update(['tanggal_diterima' => now()]);
        }

        return back()->with('success', 'Status pengiriman berhasil diperbarui!');
    }

    /**
     * List surat pengiriman untuk sopir
     */
    public function listSuratSopir()
    {
        $pengiriman = Pengiriman::with([
            'transaksi.pembeli',
            'transaksi.nelayan',
            'sopir'
        ])->where('sopir_id', Auth::id())
            ->orderBy('tanggal_kirim', 'desc')
            ->paginate(15);

        return view('dashboard.sopir.list-surat', compact('pengiriman'));
    }

    /**
     * List semua surat pengiriman (admin only)
     */
    public function listSuratAdmin(Request $request)
    {
        $query = Pengiriman::with([
            'transaksi.pembeli',
            'transaksi.nelayan',
            'sopir'
        ]);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by sopir
        if ($request->has('sopir_id') && $request->sopir_id != '') {
            $query->where('sopir_id', $request->sopir_id);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nomor_resi', 'like', "%{$search}%")
                  ->orWhere('alamat_tujuan', 'like', "%{$search}%");
        }

        $pengiriman = $query->orderBy('tanggal_kirim', 'desc')->paginate(20);
        $sopir = Pengguna::where('peran', 'sopir')->get();

        return view('dashboard.admin.list-surat-pengiriman', compact('pengiriman', 'sopir'));
    }
}
