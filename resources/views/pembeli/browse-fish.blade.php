@extends('layouts.app')

@section('title', 'Jelajahi Ikan Segar')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Jelajahi Ikan Segar</h1>
        <p class="text-gray-600 mt-2">Pilih ikan berkualitas tinggi dari nelayan lokal</p>
    </div>

    <!-- Cart Counter -->
    <div class="mb-6 flex justify-end">
        <a href="{{ route('pembeli.cart.view') }}" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <span>🛒 Keranjang</span>
            <span id="cart-count" class="bg-white text-blue-600 rounded-full w-6 h-6 flex items-center justify-center font-bold text-sm">0</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('pembeli.browse-fish') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Ikan</label>
                <input type="text" name="jenis_ikan" value="{{ request('jenis_ikan') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Cari jenis ikan...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Min (Rp)</label>
                <input type="number" name="min_harga" value="{{ request('min_harga') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Max (Rp)</label>
                <input type="number" name="max_harga" value="{{ request('max_harga') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="9999999">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kualitas</label>
                <select name="kualitas" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kualitas</option>
                    <option value="A" {{ request('kualitas') === 'A' ? 'selected' : '' }}>A (Premium)</option>
                    <option value="B" {{ request('kualitas') === 'B' ? 'selected' : '' }}>B (Standar)</option>
                    <option value="C" {{ request('kualitas') === 'C' ? 'selected' : '' }}>C (Ekonomi)</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Fish Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($ikans as $ikan)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition transform hover:scale-105">
            <!-- Foto -->
            <div class="relative w-full h-48 bg-gray-200 overflow-hidden">
                @if($ikan->foto_ikan)
                    <img src="{{ asset('storage/' . $ikan->foto_ikan) }}" alt="{{ $ikan->jenis_ikan }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-300">
                        <span class="text-gray-500">No Image</span>
                    </div>
                @endif
                <!-- Quality Badge -->
                <span class="absolute top-2 right-2 px-3 py-1 rounded-full text-sm font-bold text-white
                    @if($ikan->kualitas == 'A') bg-green-600
                    @elseif($ikan->kualitas == 'B') bg-blue-600
                    @else bg-yellow-600
                    @endif">
                    Kualitas {{ $ikan->kualitas }}
                </span>
            </div>

            <!-- Content -->
            <div class="p-4">
                <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $ikan->jenis_ikan }}</h3>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $ikan->deskripsi }}</p>

                <!-- Nelayan Info -->
                <div class="mb-3 text-sm text-gray-600">
                    <p class="font-medium text-gray-700">{{ $ikan->nelayan->nama }}</p>
                    <p>📍 {{ $ikan->nelayan->alamat ?? 'Alamat tidak tersedia' }}</p>
                </div>

                <!-- Price -->
                <div class="mb-4 border-t pt-3">
                    <div class="text-2xl font-bold text-blue-600">
                        Rp {{ number_format($ikan->harga_per_kg, 0, ',', '.') }}<span class="text-sm">/kg</span>
                    </div>
                    <div class="text-xs text-gray-600">
                        Stok: {{ $ikan->berat_total_kg ?? 'Unlimited' }} kg
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <a href="{{ route('pembeli.fish-detail', $ikan->id) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 rounded text-center text-sm">
                        Detail
                    </a>
                    <button type="button" onclick="quickAddToCart({{ $ikan->id }}, '{{ $ikan->jenis_ikan }}')" 
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-3 rounded text-sm">
                        + Keranjang
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <div class="text-gray-500 mb-4">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 012.646 2.646"/>
                </svg>
            </div>
            <p class="text-xl font-semibold text-gray-600">Ikan tidak ditemukan</p>
            <p class="text-gray-500">Coba ubah filter pencarian Anda</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $ikans->links() }}
    </div>
</div>

<!-- Quick Add Modal -->
<div id="quickAddModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
        <h3 class="text-lg font-bold mb-4">Tambah ke Keranjang</h3>
        <input type="hidden" id="quickAddIkanId">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah (kg)</label>
            <input type="number" id="quickAddJumlah" value="1" min="1" max="1000" step="0.5"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mt-6 flex gap-3">
            <button type="button" onclick="confirmQuickAdd()" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded">
                Tambah
            </button>
            <button type="button" onclick="closeQuickAddModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 rounded">
                Batal
            </button>
        </div>
    </div>
</div>

<script>
function quickAddToCart(ikanId, nama) {
    document.getElementById('quickAddIkanId').value = ikanId;
    document.getElementById('quickAddJumlah').value = '1';
    document.getElementById('quickAddModal').classList.remove('hidden');
}

function closeQuickAddModal() {
    document.getElementById('quickAddModal').classList.add('hidden');
}

function confirmQuickAdd() {
    const ikanId = document.getElementById('quickAddIkanId').value;
    const jumlah = document.getElementById('quickAddJumlah').value;

    fetch('{{ route('pembeli.cart.add') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            jumlah_kg: parseFloat(jumlah)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✓ ' + data.message);
            updateCartCount();
            closeQuickAddModal();
        } else {
            alert('✗ Error: ' + data.error);
        }
    })
    .catch(error => alert('Error: ' + error));
}

function updateCartCount() {
    // Fetch current cart count
    const cart = JSON.parse(sessionStorage.getItem('cart') || '[]');
    document.getElementById('cart-count').textContent = cart.length;
}

// Initialize on load
document.addEventListener('DOMContentLoaded', updateCartCount);
</script>
@endsection
