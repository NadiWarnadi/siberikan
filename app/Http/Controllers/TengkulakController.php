<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Penawaran;
use App\Models\Pengiriman;

class TengkulakController extends Controller
{
    public function index(Request $request)
    {
        // Penawaran menunggu persetujuan (pending)
        $penawaranPending = Penawaran::with(['jenisIkan', 'nelayan'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Penawaran yang sudah disetujui (approved)
        $queryApproved = Penawaran::with(['jenisIkan', 'nelayan'])
            ->where('status', 'approved');

        // Filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $queryApproved->whereHas('jenisIkan', function($q) use ($search) {
                $q->where('nama_ikan', 'like', "%{$search}%");
            });
        }

        if ($request->has('kualitas') && $request->kualitas != '') {
            $queryApproved->where('kualitas', $request->kualitas);
        }

        $penawaranApproved = $queryApproved->orderBy('created_at', 'desc')->paginate(10);

        // Transactions managed by this tengkulak
        $transaksi = Transaksi::with(['pembeli', 'details.hasilTangkapan.jenisIkan'])
            ->where('tengkulak_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Shipments
        $pengiriman = Pengiriman::with(['transaksi', 'sopir'])
            ->whereHas('transaksi', function($q) {
                $q->where('tengkulak_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.tengkulak', compact('penawaranPending', 'penawaranApproved', 'transaksi', 'pengiriman'));
    }
}
