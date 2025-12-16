<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\HasilTangkapan;
use App\Models\Pengiriman;

class TengkulakController extends Controller
{
    public function index(Request $request)
    {
        // Available stock from all fishermen
        $stokTersedia = HasilTangkapan::with(['jenisIkan', 'nelayan'])
            ->where('status', 'tersedia');

        // Filter stock
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $stokTersedia->whereHas('jenisIkan', function($q) use ($search) {
                $q->where('nama_ikan', 'like', "%{$search}%");
            });
        }

        if ($request->has('grade') && $request->grade != '') {
            $stokTersedia->where('grade', $request->grade);
        }

        $stokTersedia = $stokTersedia->orderBy('created_at', 'desc')->paginate(10);

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

        return view('dashboard.tengkulak', compact('stokTersedia', 'transaksi', 'pengiriman'));
    }
}
