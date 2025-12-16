<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Penawaran;
use App\Models\HasilTangkapan;
use App\Models\MasterJenisIkan;

class PenawaranController extends Controller
{
    /**
     * Tampilkan form buat penawaran
     */
    public function showCreateForm()
    {
        $jenisIkan = MasterJenisIkan::all();
        return view('dashboard.nelayan.create-penawaran', compact('jenisIkan'));
    }

    /**
     * Nelayan membuat penawaran baru
     */
    public function createPenawaran(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'jenis_ikan_id' => 'required|exists:master_jenis_ikan,id',
            'jumlah_kg' => 'required|numeric|min:0.1',
            'harga_per_kg' => 'required|numeric|min:1000|max:999999',
            'kualitas' => 'required|in:premium,standar,ekonomis',
            'lokasi_tangkapan' => 'nullable|string|max:255',
            'kedalaman' => 'nullable|string|max:50',
            'tanggal_tangkapan' => 'required|date|before_or_equal:today',
            'catatan' => 'nullable|string|max:1000',
            'foto_ikan' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Security: Check user is nelayan
        if (Auth::user()->peran != 'nelayan') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Security: Sanitize harga_per_kg
        $hargaPerKg = (int) $validated['harga_per_kg'];
        if ($hargaPerKg < 1000 || $hargaPerKg > 999999) {
            return back()->with('error', 'Harga tidak valid. Range: Rp 1.000 - Rp 999.999');
        }

        try {
            DB::beginTransaction();

            // Generate kode penawaran
            $kodePenawaran = 'PWR-' . date('YmdHis') . '-' . strtoupper(Str::random(4));

            // Upload foto
            $fotoPath = null;
            if ($request->hasFile('foto_ikan')) {
                $file = $request->file('foto_ikan');
                $filename = 'penawaran_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $fotoPath = $file->storeAs('penawarans', $filename, 'public');
            }

            // Create penawaran
            $penawaran = Penawaran::create([
                'kode_penawaran' => $kodePenawaran,
                'nelayan_id' => Auth::id(),
                'jenis_ikan_id' => $validated['jenis_ikan_id'],
                'jumlah_kg' => $validated['jumlah_kg'],
                'harga_per_kg' => $hargaPerKg,
                'kualitas' => $validated['kualitas'],
                'lokasi_tangkapan' => $validated['lokasi_tangkapan'],
                'kedalaman' => $validated['kedalaman'],
                'tanggal_tangkapan' => $validated['tanggal_tangkapan'],
                'catatan' => $validated['catatan'],
                'foto_ikan' => $fotoPath,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('nelayan.dashboard')
                ->with('success', 'Penawaran berhasil dibuat! Kode: ' . $kodePenawaran . '. Tunggu persetujuan dari Tengkulak.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating penawaran: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat penawaran: ' . $e->getMessage());
        }
    }

    /**
     * Nelayan lihat list penawarannya
     */
    public function listPenawaranNelayan(Request $request)
    {
        $query = Penawaran::with(['jenisIkan', 'approver'])
            ->where('nelayan_id', Auth::id());

        // Filter status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('kode_penawaran', 'like', "%{$search}%");
        }

        $penawarans = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('dashboard.nelayan.list-penawarans', compact('penawarans'));
    }

    /**
     * Nelayan lihat detail penawaran
     */
    public function detailPenawaran($id)
    {
        $penawaran = Penawaran::findOrFail($id);

        // Check ownership
        if ($penawaran->nelayan_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('dashboard.nelayan.detail-penawaran', compact('penawaran'));
    }

    /**
     * Nelayan edit penawaran (hanya jika belum approved)
     */
    public function editPenawaran(Request $request, $id)
    {
        $penawaran = Penawaran::findOrFail($id);

        // Check ownership
        if ($penawaran->nelayan_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check status
        if ($penawaran->status != 'draft' && $penawaran->status != 'rejected') {
            return back()->with('error', 'Penawaran ini tidak bisa diedit');
        }

        // Validate input
        $validated = $request->validate([
            'jumlah_kg' => 'required|numeric|min:0.1',
            'harga_per_kg' => 'required|numeric|min:1000|max:999999',
            'kualitas' => 'required|in:premium,standar,ekonomis',
            'catatan' => 'nullable|string|max:1000',
            'foto_ikan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        try {
            // Security: Validate harga
            $hargaPerKg = (int) $validated['harga_per_kg'];
            if ($hargaPerKg < 1000 || $hargaPerKg > 999999) {
                return back()->with('error', 'Harga tidak valid');
            }

            // Update foto jika ada
            if ($request->hasFile('foto_ikan')) {
                if ($penawaran->foto_ikan) {
                    \Storage::disk('public')->delete($penawaran->foto_ikan);
                }
                $file = $request->file('foto_ikan');
                $filename = 'penawaran_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $fotoPath = $file->storeAs('penawarans', $filename, 'public');
                $penawaran->foto_ikan = $fotoPath;
            }

            // Update penawaran
            $penawaran->update([
                'jumlah_kg' => $validated['jumlah_kg'],
                'harga_per_kg' => $hargaPerKg,
                'kualitas' => $validated['kualitas'],
                'catatan' => $validated['catatan'],
                'status' => 'draft',
            ]);

            return redirect()->route('nelayan.list-penawarans')
                ->with('success', 'Penawaran berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update penawaran: ' . $e->getMessage());
        }
    }

    /**
     * Nelayan submit penawaran (dari draft ke pending)
     */
    public function submitPenawaran($id)
    {
        $penawaran = Penawaran::findOrFail($id);

        // Check ownership
        if ($penawaran->nelayan_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check status harus draft
        if ($penawaran->status != 'draft') {
            return back()->with('error', 'Penawaran ini sudah disubmit');
        }

        $penawaran->update(['status' => 'pending']);

        return back()->with('success', 'Penawaran berhasil disubmit untuk persetujuan!');
    }

    /**
     * Nelayan batal penawaran
     */
    public function cancelPenawaran($id)
    {
        $penawaran = Penawaran::findOrFail($id);

        // Check ownership
        if ($penawaran->nelayan_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check status
        if (in_array($penawaran->status, ['approved', 'rejected', 'canceled'])) {
            return back()->with('error', 'Penawaran ini tidak bisa dibatalkan');
        }

        $penawaran->update(['status' => 'canceled']);

        return back()->with('success', 'Penawaran berhasil dibatalkan!');
    }
}
