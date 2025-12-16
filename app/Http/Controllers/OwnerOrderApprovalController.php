<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Pengiriman;

class OwnerOrderApprovalController extends Controller
{
    /**
     * List pesanan pending approval
     */
    public function pendingOrders()
    {
        // Security: Hanya owner
        if (Auth::user()->peran != 'owner') {
            abort(403, 'Hanya owner yang bisa akses');
        }

        $transaksis = Transaksi::where('owner_id', Auth::id())
            ->where('status_transaksi', 'pending')
            ->with(['pembeli', 'nelayan', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'pending' => Transaksi::where('owner_id', Auth::id())
                ->where('status_transaksi', 'pending')
                ->count(),
            'approved' => Transaksi::where('owner_id', Auth::id())
                ->where('status_transaksi', 'approved')
                ->count(),
            'rejected' => Transaksi::where('owner_id', Auth::id())
                ->where('status_transaksi', 'rejected')
                ->count(),
            'shipped' => Transaksi::where('owner_id', Auth::id())
                ->where('status_transaksi', 'shipped')
                ->count(),
        ];

        return view('owner.orders-pending', compact('transaksis', 'stats'));
    }

    /**
     * Lihat detail pesanan sebelum ACC
     */
    public function showOrder($id)
    {
        if (Auth::user()->peran != 'owner') {
            abort(403, 'Unauthorized');
        }

        $transaksi = Transaksi::with(['pembeli', 'nelayan', 'items', 'pengirimen'])
            ->findOrFail($id);

        // Check ownership
        if ($transaksi->owner_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('owner.order-detail', compact('transaksi'));
    }

    /**
     * ACC (Approve) pesanan
     */
    public function approve(Request $request, $id)
    {
        if (Auth::user()->peran != 'owner') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'catatan_approval' => 'nullable|string|max:500',
        ]);

        try {
            $transaksi = Transaksi::findOrFail($id);

            // Check ownership
            if ($transaksi->owner_id != Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Check status pending
            if ($transaksi->status_transaksi != 'pending') {
                return response()->json(['error' => 'Pesanan tidak dalam status pending'], 400);
            }

            \DB::beginTransaction();

            // Update transaksi status to approved
            $transaksi->update([
                'status_transaksi' => 'approved',
                'catatan_approval' => $validated['catatan_approval'] ?? null,
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);

            // Create pengiriman record (ready for sopir pickup)
            $pengiriman = Pengiriman::create([
                'transaksi_id' => $transaksi->id,
                'sopir_id' => null, // Will be assigned later
                'status_pengiriman' => 'pending',
                'nomor_resi' => 'RES-' . time() . '-' . random_int(1000, 9999),
                'alamat_pengiriman' => $transaksi->alamat_pengiriman,
            ]);

            \DB::commit();

            \Log::info('Pesanan disetujui oleh owner', [
                'transaksi_id' => $transaksi->id,
                'owner_id' => Auth::id(),
                'pengiriman_id' => $pengiriman->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil disetujui! Siap untuk pengiriman.',
                'pengiriman_id' => $pengiriman->id,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error approving order: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Reject pesanan
     */
    public function reject(Request $request, $id)
    {
        if (Auth::user()->peran != 'owner') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        try {
            $transaksi = Transaksi::findOrFail($id);

            // Check ownership
            if ($transaksi->owner_id != Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Check status pending
            if ($transaksi->status_transaksi != 'pending') {
                return response()->json(['error' => 'Pesanan tidak dalam status pending'], 400);
            }

            $transaksi->update([
                'status_transaksi' => 'rejected',
                'alasan_penolakan' => $validated['alasan_penolakan'],
                'rejected_at' => now(),
                'rejected_by' => Auth::id(),
            ]);

            \Log::info('Pesanan ditolak oleh owner', [
                'transaksi_id' => $transaksi->id,
                'owner_id' => Auth::id(),
                'reason' => $validated['alasan_penolakan'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil ditolak.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Lihat pesanan yang sudah di-ACC (ready for shipping)
     */
    public function approvedOrders()
    {
        if (Auth::user()->peran != 'owner') {
            abort(403, 'Unauthorized');
        }

        $transaksis = Transaksi::where('owner_id', Auth::id())
            ->where('status_transaksi', 'approved')
            ->with(['pembeli', 'nelayan', 'pengirimen.sopir'])
            ->orderBy('approved_at', 'desc')
            ->paginate(15);

        return view('owner.orders-approved', compact('transaksis'));
    }

    /**
     * Lihat history pesanan (approved + rejected)
     */
    public function orderHistory(Request $request)
    {
        if (Auth::user()->peran != 'owner') {
            abort(403, 'Unauthorized');
        }

        $query = Transaksi::where('owner_id', Auth::id())
            ->whereIn('status_transaksi', ['approved', 'rejected', 'shipped'])
            ->with(['pembeli', 'nelayan', 'pengirimen.sopir']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status_transaksi', $request->status);
        }

        $transaksis = $query->orderBy('created_at', 'desc')->paginate(15);

        $statuses = ['approved', 'rejected', 'shipped'];

        return view('owner.orders-history', compact('transaksis', 'statuses'));
    }

    /**
     * Get stats untuk dashboard
     */
    public function getStats()
    {
        if (Auth::user()->peran != 'owner') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = [
            'pending' => Transaksi::where('owner_id', Auth::id())
                ->where('status_transaksi', 'pending')
                ->count(),
            'approved' => Transaksi::where('owner_id', Auth::id())
                ->where('status_transaksi', 'approved')
                ->count(),
            'rejected' => Transaksi::where('owner_id', Auth::id())
                ->where('status_transaksi', 'rejected')
                ->count(),
            'shipped' => Transaksi::where('owner_id', Auth::id())
                ->where('status_transaksi', 'shipped')
                ->count(),
            'total_value' => \DB::table('transaksis')
                ->where('owner_id', Auth::id())
                ->where('status_transaksi', 'approved')
                ->sum('total_harga'),
        ];

        return response()->json($stats);
    }
}
