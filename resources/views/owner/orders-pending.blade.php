@extends('layouts.app')

@section('title', 'Pesanan Pending - Owner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Pesanan Menunggu Persetujuan</h1>
        <p class="text-gray-600 mt-2">Review dan setujui pesanan dari pembeli</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
            <div class="text-sm text-gray-600">Pending</div>
            <div class="text-2xl font-bold text-red-600">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="text-sm text-gray-600">Disetujui</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</div>
        </div>
        <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
            <div class="text-sm text-gray-600">Ditolak</div>
            <div class="text-2xl font-bold text-orange-600">{{ $stats['rejected'] }}</div>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="text-sm text-gray-600">Dikirim</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['shipped'] }}</div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
        @forelse($transaksis as $transaksi)
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <!-- Order Info -->
                <div>
                    <div class="text-sm text-gray-600">Order ID</div>
                    <div class="text-lg font-bold text-gray-800">#{{ $transaksi->id }}</div>
                    <div class="text-sm text-gray-600 mt-2">Tanggal: {{ $transaksi->created_at->format('d M Y H:i') }}</div>
                </div>

                <!-- Pembeli Info -->
                <div>
                    <div class="text-sm text-gray-600">Pembeli</div>
                    <div class="text-lg font-bold text-gray-800">{{ $transaksi->pembeli->nama }}</div>
                    <div class="text-sm text-gray-600">📧 {{ $transaksi->pembeli->email }}</div>
                    <div class="text-sm text-gray-600">📱 {{ $transaksi->pembeli->no_telepon ?? '-' }}</div>
                </div>

                <!-- Nelayan Info -->
                <div>
                    <div class="text-sm text-gray-600">Nelayan</div>
                    <div class="text-lg font-bold text-gray-800">{{ $transaksi->nelayan->nama }}</div>
                    <div class="text-sm text-gray-600">📧 {{ $transaksi->nelayan->email }}</div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-4 border-t pt-4">
                <h4 class="font-semibold text-gray-800 mb-3">Daftar Pesanan:</h4>
                <div class="space-y-2">
                    @foreach($transaksi->items as $item)
                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded">
                        <div>
                            <p class="font-medium text-gray-800">{{ $item->penawaran->jenis_ikan ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600">{{ $item->jumlah_kg }} kg × Rp {{ number_format($item->harga_per_kg, 0, ',', '.') }}/kg</p>
                        </div>
                        <div class="text-right font-bold">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="mb-4 bg-blue-50 p-4 rounded border border-blue-200">
                <p class="text-sm text-gray-600 font-medium">Alamat Pengiriman:</p>
                <p class="text-gray-800">{{ $transaksi->alamat_pengiriman }}</p>
            </div>

            <!-- Total & Payment -->
            <div class="mb-4 flex justify-between items-center border-t pt-4">
                <div>
                    <p class="text-sm text-gray-600">Metode Pembayaran: <span class="font-semibold text-gray-800">{{ ucfirst($transaksi->metode_pembayaran) }}</span></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Total Pesanan</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Notes -->
            @if($transaksi->catatan)
            <div class="mb-4 bg-yellow-50 p-3 rounded border border-yellow-200">
                <p class="text-sm text-gray-600 font-medium">Catatan Pembeli:</p>
                <p class="text-gray-800">{{ $transaksi->catatan }}</p>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('owner.orders.show', $transaksi->id) }}" 
                    class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                    Lihat Detail
                </a>
                <button type="button" onclick="approveOrder({{ $transaksi->id }})"
                    class="flex-1 md:flex-none bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded">
                    ✓ Setujui
                </button>
                <button type="button" onclick="rejectOrder({{ $transaksi->id }})"
                    class="flex-1 md:flex-none bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded">
                    ✗ Tolak
                </button>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <div class="text-gray-500 mb-4">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                </svg>
            </div>
            <p class="text-xl font-semibold text-gray-600">Tidak ada pesanan pending</p>
            <p class="text-gray-500">Semua pesanan sudah diproses</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $transaksis->links() }}
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold mb-4">Setujui Pesanan</h3>
        <input type="hidden" id="approveTransaksiId">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (opsional)</label>
            <textarea id="approveNotes" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Tambahkan catatan approval..."></textarea>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="button" onclick="confirmApprove()" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded">
                Setujui
            </button>
            <button type="button" onclick="closeModal('approveModal')" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 rounded">
                Batal
            </button>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold mb-4">Tolak Pesanan</h3>
        <input type="hidden" id="rejectTransaksiId">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-600">*</span></label>
            <textarea id="rejectReason" rows="3" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                placeholder="Jelaskan alasan penolakan..."></textarea>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="button" onclick="confirmReject()" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded">
                Tolak
            </button>
            <button type="button" onclick="closeModal('rejectModal')" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 rounded">
                Batal
            </button>
        </div>
    </div>
</div>

<script>
function approveOrder(transaksiId) {
    document.getElementById('approveTransaksiId').value = transaksiId;
    document.getElementById('approveNotes').value = '';
    document.getElementById('approveModal').classList.remove('hidden');
}

function rejectOrder(transaksiId) {
    document.getElementById('rejectTransaksiId').value = transaksiId;
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function confirmApprove() {
    const transaksiId = document.getElementById('approveTransaksiId').value;
    const notes = document.getElementById('approveNotes').value;

    fetch(`/owner/orders/${transaksiId}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            catatan_approval: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✓ ' + data.message);
            location.reload();
        } else {
            alert('✗ Error: ' + data.error);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
        console.error(error);
    });
}

function confirmReject() {
    const transaksiId = document.getElementById('rejectTransaksiId').value;
    const reason = document.getElementById('rejectReason').value;

    if (!reason.trim()) {
        alert('Silakan masukkan alasan penolakan');
        return;
    }

    fetch(`/owner/orders/${transaksiId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            alasan_penolakan: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✓ ' + data.message);
            location.reload();
        } else {
            alert('✗ Error: ' + data.error);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
        console.error(error);
    });
}
</script>
@endsection
