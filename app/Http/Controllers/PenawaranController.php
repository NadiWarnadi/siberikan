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
        // Comprehensive validation with security rules
        $validated = $request->validate([
            'jenis_ikan_id' => 'required|integer|exists:master_jenis_ikan,id',
            'jumlah_kg' => 'required|numeric|between:0.1,999999|regex:/^\d+(\.\d{1,2})?$/',
            'harga_per_kg' => 'required|integer|between:1000,999999',
            'kualitas' => 'required|in:premium,standar,ekonomi',
            'lokasi_tangkapan' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9\s\-.,()]+$/',
            'kedalaman' => 'nullable|string|max:10|regex:/^\d+$/',
            'tanggal_tangkapan' => 'required|date|before_or_equal:today',
            'catatan' => 'nullable|string|max:1000|regex:/^[a-zA-Z0-9\s\-.,()]+$/',
            'foto_ikan' => 'required|image|mimes:jpeg,png,jpg|max:5120|dimensions:min_width=300,min_height=300',
        ], [
            'jenis_ikan_id.required' => 'Jenis ikan harus dipilih',
            'jenis_ikan_id.exists' => 'Jenis ikan tidak valid',
            'jumlah_kg.required' => 'Jumlah kg harus diisi',
            'jumlah_kg.numeric' => 'Jumlah kg harus berupa angka',
            'jumlah_kg.between' => 'Jumlah kg harus antara 0.1 dan 999999',
            'jumlah_kg.regex' => 'Format jumlah tidak valid (max 2 desimal)',
            'harga_per_kg.required' => 'Harga per kg harus diisi',
            'harga_per_kg.integer' => 'Harga harus berupa angka bulat',
            'harga_per_kg.between' => 'Harga harus antara Rp 1.000 - Rp 999.999',
            'kualitas.required' => 'Kualitas harus dipilih',
            'kualitas.in' => 'Kualitas tidak valid',
            'foto_ikan.required' => 'Foto ikan harus diunggah',
            'foto_ikan.image' => 'File harus berupa gambar',
            'foto_ikan.mimes' => 'Format gambar harus JPEG atau PNG',
            'foto_ikan.max' => 'Ukuran gambar maksimal 5MB',
            'foto_ikan.dimensions' => 'Ukuran gambar minimal 300x300 pixel',
            'lokasi_tangkapan.regex' => 'Lokasi mengandung karakter tidak diizinkan',
            'catatan.regex' => 'Catatan mengandung karakter tidak diizinkan',
        ]);

        // Security: Verify user is nelayan
        if (Auth::user()->peran !== 'nelayan') {
            abort(403, 'Unauthorized to create penawaran');
        }

        // Security: Prevent rapid duplicate submissions
        $recentSubmission = Penawaran::where('nelayan_id', Auth::id())
            ->where('created_at', '>=', now()->subMinutes(1))
            ->exists();

        if ($recentSubmission) {
            return back()->with('error', 'Terlalu sering membuat penawaran. Tunggu 1 menit.');
        }

        try {
            DB::beginTransaction();

            // Generate unique kode penawaran with crypto-random
            $kodePenawaran = 'PWR-' . date('YmdHis') . '-' . strtoupper(Str::random(6));

            // Upload foto dengan security checks
            $fotoPath = null;
            if ($request->hasFile('foto_ikan')) {
                $file = $request->file('foto_ikan');

                // Additional security: Verify MIME type
                if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
                    throw new \Exception('Invalid image format detected');
                }

                // Generate safe filename
                $filename = 'penawaran_' . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $file->getClientOriginalExtension();
                
                // Store with specific permissions
                $fotoPath = $file->storeAs('penawarans', $filename, 'public');

                if (!$fotoPath) {
                    throw new \Exception('File upload gagal');
                }
            }

            // Create penawaran with sanitized data
            $penawaran = Penawaran::create([
                'kode_penawaran' => $kodePenawaran,
                'nelayan_id' => Auth::id(),
                'jenis_ikan_id' => (int) $validated['jenis_ikan_id'],
                'jumlah_kg' => round((float) $validated['jumlah_kg'], 2),
                'harga_per_kg' => (int) $validated['harga_per_kg'],
                'kualitas' => strtolower($validated['kualitas']),
                'lokasi_tangkapan' => htmlspecialchars($validated['lokasi_tangkapan'] ?? '', ENT_QUOTES, 'UTF-8'),
                'kedalaman' => (int) ($validated['kedalaman'] ?? 0),
                'tanggal_tangkapan' => $validated['tanggal_tangkapan'],
                'catatan' => htmlspecialchars($validated['catatan'] ?? '', ENT_QUOTES, 'UTF-8'),
                'foto_ikan' => $fotoPath,
                'status' => 'pending',
            ]);

            DB::commit();

            \Log::info('Penawaran created', ['penawaran_id' => $penawaran->id, 'nelayan_id' => Auth::id()]);

            return redirect()->route('nelayan.dashboard')
                ->with('success', 'Penawaran berhasil dibuat! Kode: ' . htmlspecialchars($kodePenawaran) . '. Menunggu persetujuan.');

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error creating penawaran', [
                'error' => $e->getMessage(),
                'nelayan_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->with('error', 'Gagal membuat penawaran. Silakan coba lagi.');
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
