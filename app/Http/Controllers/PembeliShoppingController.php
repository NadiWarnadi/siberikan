<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Penawaran;
use App\Models\Transaksi;

class PembeliShoppingController extends Controller
{
    /**
     * Browse ikan dengan foto & harga
     */
    public function browse(Request $request)
    {
        // Security: Hanya pembeli
        if (Auth::user()->peran != 'pembeli') {
            abort(403, 'Hanya pembeli yang bisa akses');
        }

        $query = Penawaran::where('status_penawaran', 'approved')
            ->with('nelayan');

        // Filter by jenis ikan
        if ($request->has('jenis_ikan') && $request->jenis_ikan != '') {
            $query->where('jenis_ikan', 'like', '%' . $request->jenis_ikan . '%');
        }

        // Filter by harga range
        if ($request->has('min_harga') && $request->min_harga != '') {
            $query->where('harga_per_kg', '>=', $request->min_harga);
        }

        if ($request->has('max_harga') && $request->max_harga != '') {
            $query->where('harga_per_kg', '<=', $request->max_harga);
        }

        // Filter by kualitas
        if ($request->has('kualitas') && $request->kualitas != '') {
            $query->where('kualitas', $request->kualitas);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('jenis_ikan', 'like', '%' . $search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        $ikans = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get filter options
        $allIkans = Penawaran::where('status_penawaran', 'approved')
            ->select('jenis_ikan')
            ->distinct()
            ->pluck('jenis_ikan');

        $kualitas_options = ['A', 'B', 'C'];

        return view('pembeli.browse-fish', compact('ikans', 'allIkans', 'kualitas_options'));
    }

    /**
     * Lihat detail ikan + tombol order
     */
    public function detail($id)
    {
        if (Auth::user()->peran != 'pembeli') {
            abort(403, 'Unauthorized');
        }

        $ikan = Penawaran::with('nelayan')->findOrFail($id);

        // Cek status approved
        if ($ikan->status_penawaran != 'approved') {
            abort(404, 'Ikan tidak tersedia');
        }

        // Get related ikans dari nelayan yang sama
        $related = Penawaran::where('nelayan_id', $ikan->nelayan_id)
            ->where('id', '!=', $ikan->id)
            ->where('status_penawaran', 'approved')
            ->limit(4)
            ->get();

        return view('pembeli.detail-fish', compact('ikan', 'related'));
    }

    /**
     * Tambah ke keranjang (session-based)
     */
    public function addToCart(Request $request, $id)
    {
        if (Auth::user()->peran != 'pembeli') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'jumlah_kg' => 'required|numeric|min:1|max:1000',
        ]);

        try {
            $ikan = Penawaran::findOrFail($id);

            if ($ikan->status_penawaran != 'approved') {
                return response()->json(['error' => 'Ikan tidak tersedia'], 400);
            }

            // Initialize cart in session
            if (!session()->has('cart')) {
                session()->put('cart', []);
            }

            $cart = session()->get('cart');

            // Check if already in cart
            $cartKey = null;
            foreach ($cart as $key => $item) {
                if ($item['ikan_id'] == $id) {
                    $cartKey = $key;
                    break;
                }
            }

            if ($cartKey !== null) {
                // Update quantity
                $cart[$cartKey]['jumlah_kg'] += $validated['jumlah_kg'];
            } else {
                // Add new item
                $cart[] = [
                    'ikan_id' => $ikan->id,
                    'nelayan_id' => $ikan->nelayan_id,
                    'jenis_ikan' => $ikan->jenis_ikan,
                    'harga_per_kg' => $ikan->harga_per_kg,
                    'jumlah_kg' => $validated['jumlah_kg'],
                    'foto_ikan' => $ikan->foto_ikan,
                ];
            }

            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Ditambahkan ke keranjang!',
                'cart_count' => count($cart),
                'cart_total' => $this->getCartTotal(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Lihat keranjang
     */
    public function viewCart()
    {
        if (Auth::user()->peran != 'pembeli') {
            abort(403, 'Unauthorized');
        }

        $cart = session()->get('cart', []);
        $total = $this->getCartTotal();
        $itemCount = count($cart);

        return view('pembeli.shopping-cart', compact('cart', 'total', 'itemCount'));
    }

    /**
     * Update item di keranjang
     */
    public function updateCart(Request $request)
    {
        if (Auth::user()->peran != 'pembeli') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'ikan_id' => 'required|exists:penamwrans,id',
            'jumlah_kg' => 'required|numeric|min:0|max:1000',
        ]);

        try {
            $cart = session()->get('cart', []);

            if ($validated['jumlah_kg'] == 0) {
                // Remove item
                $cart = array_filter($cart, function ($item) use ($validated) {
                    return $item['ikan_id'] != $validated['ikan_id'];
                });
            } else {
                // Update quantity
                foreach ($cart as &$item) {
                    if ($item['ikan_id'] == $validated['ikan_id']) {
                        $item['jumlah_kg'] = $validated['jumlah_kg'];
                        break;
                    }
                }
            }

            session()->put('cart', array_values($cart));

            return response()->json([
                'success' => true,
                'message' => 'Keranjang diperbarui!',
                'cart_total' => $this->getCartTotal(),
                'cart_count' => count($cart),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove item dari keranjang
     */
    public function removeFromCart($ikan_id)
    {
        if (Auth::user()->peran != 'pembeli') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cart = session()->get('cart', []);
        $cart = array_filter($cart, function ($item) use ($ikan_id) {
            return $item['ikan_id'] != $ikan_id;
        });

        session()->put('cart', array_values($cart));

        return response()->json([
            'success' => true,
            'message' => 'Item dihapus dari keranjang!',
            'cart_total' => $this->getCartTotal(),
            'cart_count' => count($cart),
        ]);
    }

    /**
     * Checkout dari keranjang
     */
    public function checkout(Request $request)
    {
        if (Auth::user()->peran != 'pembeli') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['error' => 'Keranjang kosong'], 400);
        }

        $validated = $request->validate([
            'alamat_pengiriman' => 'required|string|max:500',
            'metode_pembayaran' => 'required|in:transfer,cash_on_delivery',
            'catatan' => 'nullable|string|max:500',
        ]);

        try {
            \DB::beginTransaction();

            // Group by nelayan
            $groupedByNelayan = [];
            foreach ($cart as $item) {
                $nelayan_id = $item['nelayan_id'];
                if (!isset($groupedByNelayan[$nelayan_id])) {
                    $groupedByNelayan[$nelayan_id] = [];
                }
                $groupedByNelayan[$nelayan_id][] = $item;
            }

            // Buat transaksi untuk setiap nelayan
            $transaksis = [];
            foreach ($groupedByNelayan as $nelayan_id => $items) {
                $total_harga = 0;
                foreach ($items as $item) {
                    $total_harga += $item['harga_per_kg'] * $item['jumlah_kg'];
                }

                $transaksi = Transaksi::create([
                    'pembeli_id' => Auth::id(),
                    'nelayan_id' => $nelayan_id,
                    'total_harga' => $total_harga,
                    'status_transaksi' => 'pending',
                    'alamat_pengiriman' => $validated['alamat_pengiriman'],
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                // Save cart items details
                foreach ($items as $item) {
                    \DB::table('transaksi_details')->insert([
                        'transaksi_id' => $transaksi->id,
                        'penawaran_id' => $item['ikan_id'],
                        'jumlah_kg' => $item['jumlah_kg'],
                        'harga_per_kg' => $item['harga_per_kg'],
                        'subtotal' => $item['harga_per_kg'] * $item['jumlah_kg'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $transaksis[] = $transaksi;
            }

            \DB::commit();

            // Clear cart
            session()->forget('cart');

            \Log::info('Pembeli checkout', [
                'pembeli_id' => Auth::id(),
                'transaksis_count' => count($transaksis),
                'total_value' => array_sum(array_map(fn($t) => $t->total_harga, $transaksis)),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat! Menunggu konfirmasi nelayan.',
                'transaksis' => $transaksis,
                'redirect' => route('pembeli.orders'),
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Lihat pesanan pembeli
     */
    public function myOrders()
    {
        if (Auth::user()->peran != 'pembeli') {
            abort(403, 'Unauthorized');
        }

        $transaksis = Transaksi::where('pembeli_id', Auth::id())
            ->with(['nelayan', 'pengirimen'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pembeli.my-orders', compact('transaksis'));
    }

    /**
     * Helper: Get cart total
     */
    private function getCartTotal()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['harga_per_kg'] * $item['jumlah_kg'];
        }

        return $total;
    }
}
