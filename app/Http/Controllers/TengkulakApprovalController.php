<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Penawaran;
use App\Models\HasilTangkapan;

class TengkulakApprovalController extends Controller
{
    /**
     * Tengkulak Dashboard
     */
    public function dashboard(Request $request)
    {
        // Security: Check user is tengkulak
        if (Auth::user()->peran != 'tengkulak') {
            abort(403, 'Hanya Tengkulak yang bisa akses');
        }

        // Return stats if JSON requested
        if ($request->has('json') && $request->json == 'stats') {
            $stats = [
                'pending' => Penawaran::where('status', 'pending')->count(),
                'approved' => Penawaran::where('status', 'approved')->count(),
                'rejected' => Penawaran::where('status', 'rejected')->count(),
                'total' => Penawaran::count(),
            ];
            return response()->json($stats);
        }

        // Get stats
        $stats = [
            'pending' => Penawaran::where('status', 'pending')->count(),
            'approved' => Penawaran::where('status', 'approved')->count(),
            'rejected' => Penawaran::where('status', 'rejected')->count(),
            'total' => Penawaran::count(),
        ];

        return view('dashboard.tengkulak.dashboard', compact('stats'));
    }

    /**
     * Tengkulak lihat list penawaran yang pending
     */
    public function listPenawaranPending(Request $request)
    {
        // Security: Check user is tengkulak
        if (Auth::user()->peran != 'tengkulak') {
            abort(403, 'Hanya Tengkulak yang bisa akses');
        }

        $query = Penawaran::with(['nelayan', 'jenisIkan'])
            ->where('status', 'pending');

        // Filter by nelayan
        if ($request->has('nelayan_id') && $request->nelayan_id != '') {
            $query->where('nelayan_id', $request->nelayan_id);
        }

        // Filter by jenis ikan
        if ($request->has('jenis_ikan_id') && $request->jenis_ikan_id != '') {
            $query->where('jenis_ikan_id', $request->jenis_ikan_id);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('kode_penawaran', 'like', "%{$search}%");
        }

        $penawarans = $query->orderBy('created_at', 'desc')->get();

        // Get filter options
        $nelayans = \App\Models\Pengguna::where('peran', 'nelayan')->get();
        $ikans = \App\Models\MasterJenisIkan::all();

        // Calculate total amount
        $totalAmount = $penawarans->sum(function($p) {
            return $p->jumlah_kg * $p->harga_per_kg;
        });

        return view('dashboard.tengkulak.list-penawaran-pending', compact('penawarans', 'nelayans', 'ikans', 'totalAmount'));
    }

    /**
     * Tengkulak lihat detail penawaran untuk approval
     */
    public function detailPenawaranApproval($id)
    {
        // Security: Check user is tengkulak
        if (Auth::user()->peran != 'tengkulak') {
            abort(403, 'Unauthorized');
        }

        $penawaran = Penawaran::with(['nelayan', 'jenisIkan'])->findOrFail($id);

        if ($penawaran->status != 'pending') {
            return back()->with('error', 'Penawaran ini sudah diproses');
        }

        return view('dashboard.tengkulak.detail-penawaran-approval', compact('penawaran'));
    }

    /**
     * Tengkulak approve penawaran & generate invoice
     */
    public function approvePenawaran(Request $request, $id)
    {
        // Security: Check user is tengkulak
        if (Auth::user()->peran != 'tengkulak') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $penawaran = Penawaran::findOrFail($id);

        if ($penawaran->status != 'pending') {
            return response()->json(['error' => 'Penawaran ini sudah diproses'], 400);
        }

        try {
            DB::beginTransaction();

            // Update penawaran status
            $penawaran->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Create hasil tangkapan dari penawaran yang approved
            $hasilTangkapan = HasilTangkapan::create([
                'nelayan_id' => $penawaran->nelayan_id,
                'penawaran_id' => $penawaran->id,
                'jenis_ikan_id' => $penawaran->jenis_ikan_id,
                'berat' => $penawaran->jumlah_kg,
                'grade' => $penawaran->kualitas,
                'harga_per_kg' => $penawaran->harga_per_kg,
                'tanggal_tangkap' => $penawaran->tanggal_tangkapan,
                'status' => 'tersedia',
                'catatan' => $penawaran->catatan,
                'foto_ikan' => $penawaran->foto_ikan,
            ]);

            DB::commit();

            // Log activity
            \Log::info('Penawaran approved', [
                'penawaran_id' => $penawaran->id,
                'hasil_tangkapan_id' => $hasilTangkapan->id,
                'tengkulak_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penawaran berhasil disetujui!',
                'invoice_url' => route('tengkulak.generate-invoice', $penawaran->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving penawaran: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal approve penawaran: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Tengkulak reject penawaran
     */
    public function rejectPenawaran(Request $request, $id)
    {
        // Security: Check user is tengkulak
        if (Auth::user()->peran != 'tengkulak') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'alasan_reject' => 'required|string|min:10|max:500',
        ]);

        $penawaran = Penawaran::findOrFail($id);

        if ($penawaran->status != 'pending') {
            return response()->json(['error' => 'Penawaran ini sudah diproses'], 400);
        }

        // Security: Sanitize alasan
        $alasan = strip_tags($validated['alasan_reject']);

        $penawaran->update([
            'status' => 'rejected',
            'alasan_reject' => $alasan,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        \Log::info('Penawaran rejected', [
            'penawaran_id' => $penawaran->id,
            'tengkulak_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Penawaran berhasil ditolak!',
        ]);
    }

    /**
     * Tengkulak generate invoice PDF
     */
    public function generateInvoice($id)
    {
        // Security: Check user is tengkulak
        if (Auth::user()->peran != 'tengkulak') {
            abort(403, 'Unauthorized');
        }

        $penawaran = Penawaran::with(['nelayan', 'jenisIkan', 'approver'])->findOrFail($id);

        if ($penawaran->status != 'approved') {
            abort(400, 'Hanya penawaran yang sudah disetujui bisa generate invoice');
        }

        // Generate nomor invoice
        $invoiceNumber = 'INV-' . date('YmdHis') . '-' . substr(md5($penawaran->id), 0, 8);

        return view('exports.invoice-penawaran', compact('penawaran', 'invoiceNumber'));
    }

    /**
     * Tengkulak lihat history approved penawaran
     */
    public function historyApproved(Request $request)
    {
        // Security: Check user is tengkulak
        if (Auth::user()->peran != 'tengkulak') {
            abort(403, 'Unauthorized');
        }

        $query = Penawaran::with(['nelayan', 'jenisIkan', 'approver'])
            ->where('status', 'approved');

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('kode_penawaran', 'like', "%{$search}%")
                  ->orWhereHas('nelayan', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
        }

        $penawarans = $query->orderBy('approved_at', 'desc')->paginate(15);

        return view('dashboard.tengkulak.history-approved', compact('penawarans'));
    }

    /**
     * Tengkulak lihat history rejected penawaran
     */
    public function historyRejected(Request $request)
    {
        // Security: Check user is tengkulak
        if (Auth::user()->peran != 'tengkulak') {
            abort(403, 'Unauthorized');
        }

        $query = Penawaran::with(['nelayan', 'jenisIkan', 'approver'])
            ->where('status', 'rejected');

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('kode_penawaran', 'like', "%{$search}%");
        }

        $penawarans = $query->orderBy('approved_at', 'desc')->paginate(15);

        return view('dashboard.tengkulak.history-rejected', compact('penawarans'));
    }
}
