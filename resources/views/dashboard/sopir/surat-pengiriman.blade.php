@extends('layouts.app')

@section('title', 'Surat Pengiriman - SIBERIKAN')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="bi bi-file-earmark"></i> Surat Pengiriman
                <span class="badge bg-primary">{{ $pengiriman->nomor_resi }}</span>
            </h2>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('sopir.list-surat') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Status Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Status Pengiriman</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">Status Saat Ini</h6>
                                @if($pengiriman->status == 'pending')
                                    <span class="badge bg-warning py-2 px-3">PENDING</span>
                                @elseif($pengiriman->status == 'dikirim')
                                    <span class="badge bg-info py-2 px-3">DALAM PENGIRIMAN</span>
                                @elseif($pengiriman->status == 'terkirim')
                                    <span class="badge bg-success py-2 px-3">TERKIRIM</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">Tanggal Kirim</h6>
                                <strong>{{ $pengiriman->tanggal_kirim->format('d M Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">Est. Tiba</h6>
                                <strong>{{ $pengiriman->tanggal_estimasi->format('d M Y') }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    @if($pengiriman->tanggal_diterima)
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i>
                            <strong>Tanda Terima:</strong> {{ $pengiriman->tanggal_diterima->format('d M Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Detail Pengiriman -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-box"></i> Detail Pengiriman</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Penerima</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-2">
                                        <strong><i class="bi bi-person"></i> {{ $pengiriman->transaksi->pembeli->nama ?? 'Unknown' }}</strong>
                                    </p>
                                    <p class="mb-2">
                                        <i class="bi bi-envelope"></i> {{ $pengiriman->transaksi->pembeli->email ?? 'N/A' }}
                                    </p>
                                    <p class="mb-0">
                                        <i class="bi bi-geo-alt"></i> {{ $pengiriman->alamat_tujuan }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Pengirim</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-2">
                                        <strong><i class="bi bi-person"></i> {{ $pengiriman->transaksi->nelayan->nama ?? 'Unknown' }}</strong>
                                    </p>
                                    <p class="mb-2">
                                        <i class="bi bi-envelope"></i> {{ $pengiriman->transaksi->nelayan->email ?? 'N/A' }}
                                    </p>
                                    <p class="mb-0">
                                        <i class="bi bi-geo-alt"></i> {{ $pengiriman->transaksi->nelayan->alamat ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-muted mb-3">Catatan Pengiriman</h6>
                    <div class="alert alert-info">
                        {{ $pengiriman->catatan ?: 'Tidak ada catatan khusus' }}
                    </div>
                </div>
            </div>

            <!-- Detail Barang -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-fish"></i> Detail Barang</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Jenis Ikan</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengiriman->transaksi->details as $detail)
                                    <tr>
                                        <td>
                                            <strong>{{ $detail->hasilTangkapan->jenisIkan->nama_ikan ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">Tangkapan: {{ $detail->hasilTangkapan->tanggal_tangkapan->format('d M Y') }}</small>
                                        </td>
                                        <td>{{ $detail->jumlah_kg }} kg</td>
                                        <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                        <td><strong>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Total Harga:</th>
                                    <th>Rp {{ number_format($pengiriman->transaksi->total_harga, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            @if($pengiriman->status != 'terkirim')
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-pencil"></i> Update Status Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sopir.pengiriman.status', $pengiriman->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="status" class="form-label">Status Pengiriman</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    @if($pengiriman->status == 'pending')
                                        <option value="dikirim">Mulai Pengiriman (Dikirim)</option>
                                    @endif
                                    @if($pengiriman->status == 'dikirim')
                                        <option value="terkirim">Sudah Tiba (Terkirim)</option>
                                    @endif
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="catatan_update" class="form-label">Catatan Status (Opsional)</label>
                                <textarea name="catatan_update" id="catatan_update" class="form-control" rows="2"
                                          placeholder="Contoh: Sudah tiba di tangan pembeli..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Info -->
            <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted d-block mb-2">Nomor Resi:</label>
                        <h6 class="font-monospace bg-light p-2 rounded">{{ $pengiriman->nomor_resi }}</h6>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block mb-2">Kode Transaksi:</label>
                        <h6 class="font-monospace bg-light p-2 rounded">{{ $pengiriman->transaksi->kode_transaksi }}</h6>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block mb-2">Sopir Penerima:</label>
                        <p class="mb-0"><strong>{{ $pengiriman->sopir->nama ?? 'Unknown' }}</strong></p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="text-muted d-block mb-2">Total Barang:</label>
                        <h5>
                            @php
                                $totalKg = $pengiriman->transaksi->details->sum('jumlah_kg');
                            @endphp
                            {{ $totalKg }} kg
                        </h5>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block mb-2">Total Harga:</label>
                        <h5 class="text-success">
                            Rp {{ number_format($pengiriman->transaksi->total_harga, 0, ',', '.') }}
                        </h5>
                    </div>

                    <a href="{{ route('sopir.download-surat', $pengiriman->nomor_resi) }}" 
                       class="btn btn-info w-100 mb-2">
                        <i class="bi bi-download"></i> Download Surat
                    </a>

                    <a href="{{ route('sopir.list-surat') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-monospace {
        font-family: 'Courier New', Courier, monospace;
    }
</style>
@endsection
