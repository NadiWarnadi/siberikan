@extends('layouts.app')

@section('title', 'Browse Ikan - SIBERIKAN')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0"><i class="bi bi-shop"></i> Jelajahi Katalog Ikan</h2>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('pembeli.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('pembeli.browse') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Cari Ikan</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Nama ikan..." value="{{ request('search') }}">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Jenis Ikan</label>
                    <select name="jenis_ikan" class="form-select">
                        <option value="">-- Semua Jenis --</option>
                        @foreach($jenisIkan as $jenis)
                            <option value="{{ $jenis->id }}" 
                                    {{ request('jenis_ikan') == $jenis->id ? 'selected' : '' }}>
                                {{ $jenis->nama_ikan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Dari Nelayan</label>
                    <select name="nelayan" class="form-select">
                        <option value="">-- Semua Nelayan --</option>
                        @foreach($nelayan as $n)
                            <option value="{{ $n->id }}" 
                                    {{ request('nelayan') == $n->id ? 'selected' : '' }}>
                                {{ $n->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('pembeli.browse') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Ikan Listing -->
    @if($hasilTangkapan->count() > 0)
        <div class="row g-4">
            @foreach($hasilTangkapan as $ikan)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        {{ $ikan->jenisIkan->nama_ikan ?? 'N/A' }}
                                    </h5>
                                    <small class="text-muted">
                                        <i class="bi bi-person"></i> {{ $ikan->nelayan->nama ?? 'Unknown' }}
                                    </small>
                                </div>
                                <span class="badge bg-success">
                                    {{ $ikan->jumlah_kg }} kg
                                </span>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar"></i> 
                                    Tangkapan: {{ $ikan->tanggal_tangkapan->format('d M Y') }}
                                </small>
                                <small class="text-muted d-block">
                                    <i class="bi bi-geo-alt"></i> 
                                    Lokasi: {{ $ikan->lokasi_tangkapan ?? 'N/A' }}
                                </small>
                            </div>

                            <hr>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Harga/kg</small>
                                    <strong class="text-primary">
                                        Rp {{ number_format($ikan->harga_per_kg, 0, ',', '.') }}
                                    </strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Kualitas</small>
                                    <span class="badge bg-info">{{ $ikan->kualitas ?? 'Standar' }}</span>
                                </div>
                            </div>

                            @if($ikan->catatan)
                                <div class="alert alert-info py-2 mb-3">
                                    <small>{{ $ikan->catatan }}</small>
                                </div>
                            @endif

                            <a href="{{ route('pembeli.detail-ikan', $ikan->id) }}" 
                               class="btn btn-sm btn-outline-primary w-100 mb-2">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>

                            <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" 
                                    data-bs-target="#orderModal{{ $ikan->id }}">
                                <i class="bi bi-cart-plus"></i> Pesan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Order Modal -->
                <div class="modal fade" id="orderModal{{ $ikan->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Pesan {{ $ikan->jenisIkan->nama_ikan ?? 'Ikan' }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('pembeli.create-order') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <input type="hidden" name="hasil_tangkapan_id" value="{{ $ikan->id }}">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Dari Nelayan</label>
                                        <input type="text" class="form-control" disabled 
                                               value="{{ $ikan->nelayan->nama ?? 'Unknown' }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Jenis Ikan</label>
                                        <input type="text" class="form-control" disabled 
                                               value="{{ $ikan->jenisIkan->nama_ikan ?? 'N/A' }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Harga per kg</label>
                                        <input type="text" class="form-control" disabled 
                                               value="Rp {{ number_format($ikan->harga_per_kg, 0, ',', '.') }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Stok Tersedia</label>
                                        <input type="text" class="form-control" disabled 
                                               value="{{ $ikan->jumlah_kg }} kg">
                                    </div>

                                    <div class="mb-3">
                                        <label for="jumlah{{ $ikan->id }}" class="form-label">Jumlah Order (kg) *</label>
                                        <input type="number" id="jumlah{{ $ikan->id }}" name="jumlah" 
                                               class="form-control" step="0.1" min="0.1" 
                                               max="{{ $ikan->jumlah_kg }}" required>
                                        <small class="text-muted">Max: {{ $ikan->jumlah_kg }} kg</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="catatan{{ $ikan->id }}" class="form-label">Catatan Order</label>
                                        <textarea id="catatan{{ $ikan->id }}" name="catatan" class="form-control" 
                                                  rows="3" placeholder="Contoh: pengiriman hari Sabtu..."></textarea>
                                    </div>

                                    <div class="alert alert-info">
                                        <strong>Total Harga:</strong><br>
                                        <span id="totalPrice{{ $ikan->id }}" class="text-primary">
                                            Rp 0
                                        </span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Konfirmasi Order
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    document.getElementById('jumlah{{ $ikan->id }}').addEventListener('input', function() {
                        const jumlah = parseFloat(this.value) || 0;
                        const harga = {{ $ikan->harga_per_kg }};
                        const total = jumlah * harga;
                        document.getElementById('totalPrice{{ $ikan->id }}').textContent = 
                            'Rp ' + total.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                    });
                </script>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                {{ $hasilTangkapan->links() }}
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Tidak ada ikan yang tersedia saat ini. Silakan cek kembali nanti.
        </div>
    @endif
</div>

<style>
    .hover-card {
        transition: all 0.3s ease;
    }

    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
</style>
@endsection
