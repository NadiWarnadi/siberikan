<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pengiriman;

class DeliveryManagementController extends Controller
{
    /**
     * Delivery Management Dashboard (Staff/Admin)
     */
    public function index(Request $request)
    {
        // Security: Check access
        if (!in_array(Auth::user()->peran, ['admin', 'staff', 'owner'])) {
            abort(403, 'Unauthorized');
        }

        $query = Pengiriman::with(['sopir', 'transaksi']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status_pengiriman', $request->status);
        }

        // Filter by sopir
        if ($request->has('sopir_id') && $request->sopir_id != '') {
            $query->where('sopir_id', $request->sopir_id);
        }

        // Search by nomor resi
        if ($request->has('search') && $request->search != '') {
            $query->where('nomor_resi', 'like', '%' . $request->search . '%');
        }

        $pengirimen = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics
        $stats = [
            'total' => Pengiriman::count(),
            'pending' => Pengiriman::where('status_pengiriman', 'pending')->count(),
            'in_transit' => Pengiriman::where('status_pengiriman', 'in_transit')->count(),
            'delivered' => Pengiriman::where('status_pengiriman', 'delivered')->count(),
            'failed' => Pengiriman::where('status_pengiriman', 'failed')->count(),
        ];

        // Get sopir list
        $sopirs = \App\Models\Pengguna::where('peran', 'sopir')->get();

        $statuses = ['pending', 'in_transit', 'delivered', 'failed'];

        return view('admin.delivery-management.index', compact('pengirimen', 'stats', 'sopirs', 'statuses'));
    }

    /**
     * View delivery detail
     */
    public function show($id)
    {
        if (!in_array(Auth::user()->peran, ['admin', 'staff', 'owner', 'sopir'])) {
            abort(403, 'Unauthorized');
        }

        $pengiriman = Pengiriman::with(['sopir', 'transaksi', 'buktiSerahTerima'])->findOrFail($id);

        // If sopir, check if it's their delivery
        if (Auth::user()->peran == 'sopir' && $pengiriman->sopir_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('admin.delivery-management.show', compact('pengiriman'));
    }

    /**
     * Assign sopir to delivery
     */
    public function assignSopir(Request $request, $id)
    {
        if (!in_array(Auth::user()->peran, ['admin', 'staff', 'owner'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'sopir_id' => 'required|exists:penggunas,id',
        ]);

        try {
            $pengiriman = Pengiriman::findOrFail($id);

            // Cek sopir punya role sopir
            $sopir = \App\Models\Pengguna::findOrFail($validated['sopir_id']);
            if ($sopir->peran != 'sopir') {
                return response()->json(['error' => 'User bukan sopir'], 400);
            }

            $pengiriman->update([
                'sopir_id' => $validated['sopir_id'],
                'status_pengiriman' => 'in_transit',
            ]);

            \Log::info('Sopir assigned to delivery', [
                'delivery_id' => $pengiriman->id,
                'sopir_id' => $validated['sopir_id'],
                'assigned_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sopir berhasil ditugaskan!',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error assigning sopir: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update delivery status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_transit,delivered,failed',
        ]);

        try {
            $pengiriman = Pengiriman::findOrFail($id);

            // Sopir hanya bisa update ke in_transit/delivered
            if (Auth::user()->peran == 'sopir') {
                if ($pengiriman->sopir_id != Auth::id()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                if (!in_array($validated['status'], ['in_transit', 'delivered'])) {
                    return response()->json(['error' => 'Status tidak diizinkan'], 400);
                }
            }

            $pengiriman->update(['status_pengiriman' => $validated['status']]);

            \Log::info('Delivery status updated', [
                'delivery_id' => $pengiriman->id,
                'new_status' => $validated['status'],
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah!',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload bukti pengiriman (photo)
     */
    public function uploadBukti(Request $request, $id)
    {
        if (Auth::user()->peran != 'sopir') {
            return response()->json(['error' => 'Hanya sopir yang bisa upload bukti'], 403);
        }

        $validated = $request->validate([
            'foto_bukti' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'catatan' => 'nullable|string|max:500',
        ]);

        try {
            $pengiriman = Pengiriman::findOrFail($id);

            if ($pengiriman->sopir_id != Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Store photo
            $filename = time() . '_' . random_int(1000, 9999) . '.' . $validated['foto_bukti']->extension();
            $validated['foto_bukti']->storeAs('pengiriman/', $filename, 'public');

            // Create bukti serah terima record
            \DB::table('bukti_serah_terimas')->insert([
                'pengiriman_id' => $pengiriman->id,
                'foto_bukti' => 'pengiriman/' . $filename,
                'catatan' => $validated['catatan'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update pengiriman status
            $pengiriman->update(['status_pengiriman' => 'delivered']);

            \Log::info('Bukti pengiriman uploaded', [
                'delivery_id' => $pengiriman->id,
                'sopir_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti pengiriman berhasil diupload!',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error uploading bukti: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all sopir with their delivery count
     */
    public function getSopirStats()
    {
        $sopirs = \App\Models\Pengguna::where('peran', 'sopir')
            ->withCount(['pengirimen' => function ($query) {
                $query->where('status_pengiriman', 'delivered');
            }])
            ->get();

        return response()->json([
            'sopirs' => $sopirs->map(function ($s) {
                return [
                    'id' => $s->id,
                    'nama' => $s->nama,
                    'no_telepon' => $s->no_telepon,
                    'delivered_count' => $s->pengirimen_count,
                ];
            }),
        ]);
    }
}
