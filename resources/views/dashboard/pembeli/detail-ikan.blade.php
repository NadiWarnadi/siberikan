@extends('layouts.app')

@section('title', 'Detail Ikan - SIBERIKAN')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="bi bi-fish"></i> 
                {{ $ikan->jenisIkan->nama_ikan ?? 'N/A' }}
            </h2>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('pembeli.browse') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Info -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-muted mb-2">Informasi Nelayan</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-2">
                                        <strong><i class="bi bi-person"></i> Nama:</strong><br>
                                        {{ $ikan->nelayan->nama ?? 'Unknown' }}
                                    </p>
                                    <p class="mb-2">
                                        <strong><i class="bi bi-envelope"></i> Email:</strong><br>
                                        <a href="mailto:{{ $ikan->nelayan->email ?? '' }}">
                                            {{ $ikan->nelayan->email ?? 'N/A' }}
                                        </a>
                                    </p>
                                    <p class="mb-0">
                                        <strong><i class="bi bi-geo-alt"></i> Alamat:</strong><br>
                                        {{ $ikan->nelayan->alamat ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="text-muted mb-2">Informasi Tangkapan</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-2">
                                        <strong><i class="bi bi-calendar"></i> Tanggal Tangkapan:</strong><br>
                                        {{ $ikan->tanggal_tangkapan->format('d F Y H:i') }}
                                    </p>
                                    <p class="mb-2">
                                        <strong><i class="bi bi-geo-alt"></i> Lokasi Tangkapan:</strong><br>
                                        {{ $ikan->lokasi_tangkapan ?? 'Tidak Diketahui' }}
                                    </p>
                                    <p class="mb-0">
                                        <strong><i class="bi bi-bar-chart"></i> Kedalaman:</strong><br>
                                        {{ $ikan->kedalaman ?? 'Tidak Diketahui' }} meter
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5 class="text-muted mb-3">Spesifikasi Ikan</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-primary bg-opacity-10 border-primary">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">Jumlah Tersedia</small>
                                    <h4 class="text-primary mb-0">{{ $ikan->jumlah_kg }} <small>kg</small></h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card bg-success bg-opacity-10 border-success">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">Harga per kg</small>
                                    <h4 class="text-success mb-0">
                                        Rp {{ number_format($ikan->harga_per_kg, 0, ',', '.') }}
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card bg-info bg-opacity-10 border-info">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">Kualitas</small>
                                    <h4 class="text-info mb-0">{{ $ikan->kualitas ?? 'Standar' }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($ikan->catatan)
                        <div class="alert alert-info mt-3">
                            <h5 class="alert-heading"><i class="bi bi-chat"></i> Catatan</h5>
                            <p class="mb-0">{{ $ikan->catatan }}</p>
                        </div>
                    @endif

                    <div class="mt-4 p-3 bg-light rounded">
                        <h5 class="mb-3"><i class="bi bi-info-circle"></i> Status</h5>
                        <p class="mb-0">
                            <strong>Status Stok:</strong>
                            <span class="badge bg-{{ $ikan->status == 'tersedia' ? 'success' : 'danger' }}">
                                {{ ucfirst($ikan->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Section -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-cart"></i> Pesan Ikan</h5>
                </div>
                <div class="card-body">
                    @if($ikan->status == 'tersedia')
                        <form action="{{ route('pembeli.create-order') }}" method="POST">
                            @csrf
                            <input type="hidden" name="hasil_tangkapan_id" value="{{ $ikan->id }}">

                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah Order (kg)</label>
                                <div class="input-group">
                                    <input type="number" id="jumlah" name="jumlah" class="form-control" 
                                           step="0.1" min="0.1" max="{{ $ikan->jumlah_kg }}" required>
                                    <span class="input-group-text">kg</span>
                                </div>
                                <small class="text-muted">Stok: {{ $ikan->jumlah_kg }} kg</small>
                            </div>

                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan (Opsional)</label>
                                <textarea id="catatan" name="catatan" class="form-control" rows="3" 
                                          placeholder="Contoh: pengiriman hari Sabtu, hindari es berlebihan"></textarea>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="text-muted d-block">Harga per kg</label>
                                <h5 class="text-success">
                                    Rp {{ number_format($ikan->harga_per_kg, 0, ',', '.') }}
                                </h5>
                            </div>

                            <div class="mb-3 p-3 bg-light rounded">
                                <small class="text-muted d-block">Total Harga</small>
                                <h4 class="text-primary mb-0">
                                    Rp <span id="totalPrice">0</span>
                                </h4>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-check-circle"></i> Konfirmasi Order
                            </button>
                        </form>

                        <script>
                            document.getElementById('jumlah').addEventListener('input', function() {
                                const jumlah = parseFloat(this.value) || 0;
                                const harga = {{ $ikan->harga_per_kg }};
                                const total = jumlah * harga;
                                document.getElementById('totalPrice').textContent = 
                                    total.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                            });
                        </script>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Stok ikan sudah habis. Silakan lihat koleksi ikan lainnya.
                        </div>
                        <a href="{{ route('pembeli.browse') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-arrow-left"></i> Lihat Katalog Lain
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
