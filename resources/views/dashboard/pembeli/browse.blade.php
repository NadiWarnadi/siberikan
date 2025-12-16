@extends('layouts.app')

@section('title', 'Browse Ikan - SIBERIKAN')

@section('content')
<style>
    /* Responsive improvements */
    @media (max-width: 768px) {
        .card { margin-bottom: 1rem; }
        .btn { font-size: 0.9rem; padding: 0.5rem 0.8rem; }
        .product-card { margin-bottom: 1rem; }
    }

    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        transform: translateY(-4px);
    }

    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #e9ecef;
    }

    .product-body {
        padding: 1rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .product-name {
        font-weight: 700;
        font-size: 1rem;
        color: #212529;
        margin-bottom: 0.3rem;
    }

    .product-seller {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .product-specs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.85rem;
    }

    .spec-item {
        padding: 0.4rem;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .spec-label {
        display: block;
        color: #6c757d;
        font-size: 0.75rem;
    }

    .spec-value {
        font-weight: 600;
        color: #212529;
    }

    .product-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: #198754;
        margin-bottom: 0.8rem;
    }

    .product-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        margin-top: auto;
    }

    .product-actions .btn {
        font-size: 0.85rem;
        padding: 0.5rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .filter-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 2rem;
    }

    .filter-section h5 {
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .empty-catalog {
        text-align: center;
        padding: 3rem 1rem;
    }

    .empty-icon {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }
</style>

<div class="container-fluid py-3 py-md-4">
    <!-- Header -->
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="mb-0"><i class="bi bi-shop"></i> Jelajahi Ikan Segar</h2>
                <a href="{{ route('pembeli.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section mb-4">
        <h5><i class="bi bi-funnel"></i> Filter & Cari</h5>
        <form method="GET" action="{{ route('pembeli.browse') }}" class="row g-2">
            <div class="col-12 col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="Cari nama ikan..." value="{{ request('search') }}">
            </div>
            
            <div class="col-12 col-sm-6 col-md-3">
                <select name="jenis_ikan" class="form-select form-select-sm">
                    <option value="">-- Semua Jenis --</option>
                    @foreach($jenisIkan as $jenis)
                        <option value="{{ $jenis->id }}" 
                                {{ request('jenis_ikan') == $jenis->id ? 'selected' : '' }}>
                            {{ $jenis->nama_ikan }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-12 col-sm-6 col-md-3">
                <select name="nelayan" class="form-select form-select-sm">
                    <option value="">-- Semua Nelayan --</option>
                    @foreach($nelayan as $n)
                        <option value="{{ $n->id }}" 
                                {{ request('nelayan') == $n->id ? 'selected' : '' }}>
                            {{ $n->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-12 col-sm-6 col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>

            <div class="col-12 col-sm-6 col-md-auto">
                <a href="{{ route('pembeli.browse') }}" class="btn btn-secondary btn-sm w-100">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Products Grid -->
    @if($hasilTangkapan->count() > 0)
        <div class="row g-3 mb-4">
            @foreach($hasilTangkapan as $ikan)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="product-card">
                    <!-- Product Image -->
                    <div style="background: #e9ecef; position: relative;">
                        @if($ikan->foto_ikan)
                            <img src="{{ asset('storage/' . $ikan->foto_ikan) }}" class="product-image" alt="{{ $ikan->jenisIkan->nama_ikan }}">
                        @else
                            <div class="product-image d-flex align-items-center justify-content-center">
                                <i class="bi bi-fish" style="font-size: 3rem; color: #ccc;"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="product-body">
                        <div class="product-name">{{ $ikan->jenisIkan->nama_ikan ?? 'N/A' }}</div>
                        <div class="product-seller">
                            <i class="bi bi-person"></i> {{ $ikan->nelayan->nama ?? 'Unknown' }}
                        </div>

                        <!-- Specs Grid -->
                        <div class="product-specs">
                            <div class="spec-item">
                                <span class="spec-label">Stok</span>
                                <span class="spec-value">{{ number_format($ikan->berat, 1) }} kg</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Grade</span>
                                <span class="spec-value">{{ $ikan->grade }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">📍 Lokasi</span>
                                <span class="spec-value" title="{{ $ikan->lokasi_tangkapan ?? 'N/A' }}">
                                    {{ Str::limit($ikan->lokasi_tangkapan ?? 'N/A', 15) }}
                                </span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Kedalaman</span>
                                <span class="spec-value">{{ $ikan->kedalaman ?? '-' }} m</span>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="product-price">
                            Rp {{ number_format($ikan->harga_per_kg, 0, ',', '.') }}/kg
                        </div>

                        <!-- Action Buttons -->
                        <div class="product-actions">
                            <a href="{{ route('pembeli.detail-ikan', $ikan->id) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> <span class="d-none d-md-inline">Detail</span>
                            </a>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#orderModal{{ $ikan->id }}">
                                <i class="bi bi-cart-plus"></i> <span class="d-none d-md-inline">Pesan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Modal -->
            <div class="modal fade" id="orderModal{{ $ikan->id }}" tabindex="-1">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Pesan {{ $ikan->jenisIkan->nama_ikan ?? 'Ikan' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('pembeli.create-order') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="penawaran_id" value="{{ $ikan->id }}">
                                
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Order (kg)</label>
                                    <div class="input-group">
                                        <input type="number" name="jumlah" class="form-control" 
                                               step="0.1" min="0.1" max="{{ $ikan->berat }}" required
                                               placeholder="Maks: {{ number_format($ikan->berat, 1) }} kg">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                    <small class="text-muted">Stok tersedia: {{ number_format($ikan->berat, 1) }} kg</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Catatan (Opsional)</label>
                                    <textarea name="catatan" class="form-control" rows="2" 
                                              placeholder="Contoh: segera dikirm, hindari es berlebihan"></textarea>
                                </div>

                                <div class="alert alert-info p-2 mb-0">
                                    <small><strong>Total:</strong> Rp <span id="totalPrice{{ $ikan->id }}">0</span></small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-check"></i> Konfirmasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const inputJumlah = document.querySelector('#orderModal{{ $ikan->id }} input[name="jumlah"]');
                    if (inputJumlah) {
                        inputJumlah.addEventListener('input', function() {
                            const jumlah = parseFloat(this.value) || 0;
                            const harga = {{ $ikan->harga_per_kg }};
                            const total = jumlah * harga;
                            document.getElementById('totalPrice{{ $ikan->id }}').textContent = 
                                total.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                        });
                    }
                });
            </script>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($hasilTangkapan->hasPages())
        <nav aria-label="Pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                {{ $hasilTangkapan->links() }}
            </ul>
        </nav>
        @endif
    @else
        <div class="empty-catalog">
            <div class="empty-icon"><i class="bi bi-inbox"></i></div>
            <h4>Katalog Kosong</h4>
            <p class="text-muted">Tidak ada ikan yang tersedia saat ini</p>
            <a href="{{ route('pembeli.browse') }}" class="btn btn-primary btn-sm mt-3">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </a>
        </div>
    @endif
</div>

@endsection
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
                                    Tangkapan: {{ $ikan->tanggal_tangkap->format('d M Y') }}
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
