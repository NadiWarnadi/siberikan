<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HasilTangkapan;
use App\Models\MasterJenisIkan;

class NelayanController extends Controller
{
    public function index(Request $request)
    {
        $query = HasilTangkapan::with(['jenisIkan'])
            ->where('nelayan_id', Auth::id());

        // Filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('jenisIkan', function($q) use ($search) {
                $q->where('nama_ikan', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('grade') && $request->grade != '') {
            $query->where('grade', $request->grade);
        }

        $hasilTangkapan = $query->orderBy('created_at', 'desc')->paginate(10);
        $jenisIkan = MasterJenisIkan::all();

        return view('dashboard.nelayan', compact('hasilTangkapan', 'jenisIkan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_ikan_id' => 'required|exists:master_jenis_ikan,id',
            'berat' => 'required|numeric|min:0.01',
            'grade' => 'required|in:A,B,C',
            'harga_per_kg' => 'required|numeric|min:0',
            'tanggal_tangkap' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        HasilTangkapan::create([
            'nelayan_id' => Auth::id(),
            'jenis_ikan_id' => $validated['jenis_ikan_id'],
            'berat' => $validated['berat'],
            'grade' => $validated['grade'],
            'harga_per_kg' => $validated['harga_per_kg'],
            'tanggal_tangkap' => $validated['tanggal_tangkap'],
            'status' => 'tersedia',
            'catatan' => $validated['catatan'],
        ]);

        return redirect()->route('nelayan.dashboard')
            ->with('success', 'Data hasil tangkapan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $hasilTangkapan = HasilTangkapan::where('nelayan_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'jenis_ikan_id' => 'required|exists:master_jenis_ikan,id',
            'berat' => 'required|numeric|min:0.01',
            'grade' => 'required|in:A,B,C',
            'harga_per_kg' => 'required|numeric|min:0',
            'tanggal_tangkap' => 'required|date',
            'status' => 'required|in:tersedia,terjual,rusak',
            'catatan' => 'nullable|string',
        ]);

        $hasilTangkapan->update($validated);

        return redirect()->route('nelayan.dashboard')
            ->with('success', 'Data hasil tangkapan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $hasilTangkapan = HasilTangkapan::where('nelayan_id', Auth::id())
            ->findOrFail($id);
        
        $hasilTangkapan->delete();

        return redirect()->route('nelayan.dashboard')
            ->with('success', 'Data hasil tangkapan berhasil dihapus!');
    }
}
