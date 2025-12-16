@extends('layouts.app')

@section('title', 'Detail Ikan - SIBERIKAN')

@section('content')
<style>
    @media (max-width: 992px) {
        .sticky-top { position: static !important; }
        .card { margin-bottom: 1rem; }
        .btn { font-size: 0.9rem; }
    }

    @media (max-width: 576px) {
        h2 { font-size: 1.3rem; }
        .detail-image { max-height: 300px; }
        .spec-card { margin-bottom: 0.75rem; }
    }

    .detail-image {
        width: 100%;
        height: auto;
        max-height: 400px;
        object-fit: cover;
        border-radius: 12px;
        background: #e9ecef;
    }

    .info-card {
        border-left: 4px solid #0d6efd;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .spec-card {
        text-align: center;
        border-radius: 10px;
        padding: 1rem;
        transition: transform 0.2s ease;
    }

    .spec-card:hover {
        transform: translateY(-2px);
    }
</style>

<div class="container-fluid py-3 py-md-4">
    <!-- Header -->
    <div class="row mb-3 mb-md-4 align-items-center">
        <div class="col-12 col-md-8">
            <h2 class="mb-2 mb-md-0">
                <i class="bi bi-fish"></i> 
                {{ $ikan->jenisIkan->nama_ikan ?? 'N/A' }}
            </h2>
        </div>
        <div class="col-12 col-md-4 d-flex justify-content-start justify-content-md-end gap-2">
            <a href="{{ route('pembeli.browse') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-3">
        <!-- Left Column - Product Details -->
        <div class="col-lg-8">
            <!-- Product Image & Basic Info -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    @if($ikan->foto_ikan)
                        <img src="{{ asset('storage/' . $ikan->foto_ikan) }}" class="detail-image mb-3" alt="{{ $ikan->jenisIkan->nama_ikan }}">
                    @else
                        <div class="detail-image d-flex align-items-center justify-content-center mb-3">
                            <i class="bi bi-fish" style="font-size: 4rem; color: #ccc;"></i>
                        </div>
                    @endif

                    <!-- Quick Specs -->
                    <div class="row g-2 mb-3">
                        <div class="col-6 col-sm-4">
                            <div class="spec-card bg-primary bg-opacity-10">
                                <small class="text-muted d-block mb-1">Stok</small>
                                <h5 class="text-primary mb-0">{{ number_format($ikan->jumlah_kg, 1) }} <small>kg</small></h5>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="spec-card bg-success bg-opacity-10">
                                <small class="text-muted d-block mb-1">Harga/kg</small>
                                <h5 class="text-success mb-0">Rp {{ number_format($ikan->harga_per_kg, 0) }}</h5>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="spec-card bg-info bg-opacity-10">
                                <small class="text-muted d-block mb-1">Kualitas</small>
                                <h5 class="text-info mb-0">{{ strtoupper($ikan->kualitas ?? 'STD') }}</h5>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="spec-card bg-warning bg-opacity-10">
                                <small class="text-muted d-block mb-1">Grade</small>
                                <h5 class="text-warning mb-0">{{ $ikan->grade ?? '-' }}</h5>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="spec-card bg-secondary bg-opacity-10">
                                <small class="text-muted d-block mb-1">Kedalaman</small>
                                <h5 class="text-secondary mb-0">{{ $ikan->kedalaman ?? '-' }} m</h5>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="spec-card bg-danger bg-opacity-10">
                                <small class="text-muted d-block mb-1">Status</small>
                                <h5 class="text-danger mb-0">{{ ucfirst($ikan->status) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nelayan Info -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-person-badge"></i> Data Nelayan</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small">Nama Nelayan</label>
                            <p class="mb-0 fw-500">{{ $ikan->nelayan->nama ?? '-' }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small">Email</label>
                            <p class="mb-0"><a href="mailto:{{ $ikan->nelayan->email ?? '' }}">{{ $ikan->nelayan->email ?? 'N/A' }}</a></p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Alamat</label>
                            <p class="mb-0">{{ $ikan->nelayan->alamat ?? 'Tidak tersedia' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tangkapan Info -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-geo-alt"></i> Informasi Tangkapan</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small">Tanggal Tangkapan</label>
                            <p class="mb-0">{{ $ikan->tanggal_tangkap ? $ikan->tanggal_tangkap->format('d M Y H:i') : 'N/A' }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small">Lokasi</label>
                            <p class="mb-0">{{ $ikan->lokasi_tangkapan ?? 'Tidak tersedia' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan -->
            @if($ikan->catatan)
                <div class="card shadow-sm mb-3 border-start border-info border-5">
                    <div class="card-body">
                        <h6 class="mb-2"><i class="bi bi-chat"></i> Catatan Penawaran</h6>
                        <p class="mb-0 text-muted">{{ $ikan->catatan }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Order Form (Sticky on Desktop) -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px; z-index: 10;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-cart-plus"></i> Pesan Sekarang</h5>
                </div>
                <div class="card-body">
                    @if($ikan->status == 'approved' && $ikan->jumlah_kg > 0)
                        <form action="{{ route('pembeli.create-order') }}" method="POST" class="needs-validation">
                            @csrf
                            <input type="hidden" name="penawaran_id" value="{{ $ikan->id }}">

                            <!-- Jumlah Input -->
                            <div class="mb-3">
                                <label for="jumlah" class="form-label fw-500">Jumlah Pesanan (kg)</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="decreaseQty()">−</button>
                                    <input type="number" id="jumlah" name="jumlah" class="form-control text-center" 
                                           step="0.1" min="0.1" max="{{ $ikan->jumlah_kg }}" value="1" required>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="increaseQty()">+</button>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-info-circle"></i> Stok tersedia: <strong>{{ number_format($ikan->jumlah_kg, 1) }} kg</strong>
                                </small>
                            </div>

                            <!-- Catatan -->
                            <div class="mb-3">
                                <label for="catatan" class="form-label fw-500">Catatan Pesanan</label>
                                <textarea id="catatan" name="catatan" class="form-control form-control-sm" rows="2" 
                                          placeholder="Contoh: Segera dikirim, hindari es berlebihan, dll..."></textarea>
                                <small class="text-muted">Opsional - beri tahu preferensi pengiriman Anda</small>
                            </div>

                            <hr>

                            <!-- Price Summary -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Harga per kg</span>
                                    <strong>Rp {{ number_format($ikan->harga_per_kg, 0, ',', '.') }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Jumlah</span>
                                    <strong><span id="displayQty">1</span> kg</strong>
                                </div>
                                <div class="p-2 bg-light rounded mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-600">Total Harga:</span>
                                        <h5 class="text-success mb-0">
                                            Rp <span id="totalPrice">{{ number_format($ikan->harga_per_kg, 0, ',', '.') }}</span>
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success w-100 py-2 fw-600">
                                <i class="bi bi-check-circle"></i> Konfirmasi Pesanan
                            </button>

                            <a href="{{ route('pembeli.browse') }}" class="btn btn-outline-secondary w-100 mt-2 btn-sm">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </form>

                        <script>
                            const jumlahInput = document.getElementById('jumlah');
                            const hargaPerKg = {{ $ikan->harga_per_kg }};
                            const maxStok = {{ $ikan->jumlah_kg }};

                            function updateTotal() {
                                const jumlah = parseFloat(jumlahInput.value) || 0;
                                const total = jumlah * hargaPerKg;
                                document.getElementById('totalPrice').textContent = 
                                    total.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                                document.getElementById('displayQty').textContent = jumlah.toFixed(1);
                            }

                            function increaseQty() {
                                const current = parseFloat(jumlahInput.value) || 0;
                                const newVal = Math.min(current + 0.5, maxStok);
                                jumlahInput.value = newVal.toFixed(1);
                                updateTotal();
                            }

                            function decreaseQty() {
                                const current = parseFloat(jumlahInput.value) || 0;
                                const newVal = Math.max(current - 0.5, 0.1);
                                jumlahInput.value = newVal.toFixed(1);
                                updateTotal();
                            }

                            jumlahInput.addEventListener('input', updateTotal);
                            updateTotal();
                        </script>
                    @else
                        <div class="alert alert-warning" role="alert">
                            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Tidak Tersedia</h6>
                            <p class="mb-0 small">
                                @if($ikan->status != 'approved')
                                    Penawaran ini belum disetujui atau sudah ditarik kembali.
                                @else
                                    Stok ikan sudah habis.
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('pembeli.browse') }}" class="btn btn-outline-primary w-100 btn-sm">
                            <i class="bi bi-arrow-left"></i> Lihat Katalog Lain
                        </a>
                    @endif
                </div>
            </div>

            <!-- Info Box -->
            <div class="card shadow-sm mt-3 border-success">
                <div class="card-body text-center py-3">
                    <small class="text-muted d-block mb-2"><i class="bi bi-shield-check"></i> Transaksi Aman</small>
                    <p class="mb-0 small">Pembayaran & verifikasi diproses oleh SIBERIKAN</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
