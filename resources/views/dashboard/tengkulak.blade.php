@extends('layouts.app')

@section('title', 'Dashboard Tengkulak')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h2><i class="bi bi-shop"></i> Dashboard Tengkulak</h2>
            <p class="text-muted mb-0">Kelola stok ikan, transaksi, dan pengiriman</p>
        </div>
    </div>
</div>

<!-- Quick Actions Menu untuk Tengkulak (Admin) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-lightning"></i> Menu Manajemen
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-people"></i>
                            <div class="small mt-2">Manajemen Pengguna</div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.deliveries.index') }}" class="btn btn-outline-info w-100 py-3">
                            <i class="bi bi-truck"></i>
                            <div class="small mt-2">Manajemen Pengiriman</div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('penawaran.index') }}" class="btn btn-outline-success w-100 py-3">
                            <i class="bi bi-box-seam"></i>
                            <div class="small mt-2">Data Penawaran</div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-warning w-100 py-3" onclick="alert('Laporan - segera hadir'); return false;">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <div class="small mt-2">Laporan</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Stock -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-box-seam"></i> Stok Ikan Tersedia
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('tengkulak.dashboard') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari nama ikan..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="grade" class="form-select">
                                <option value="">Semua Grade</option>
                                <option value="A" {{ request('grade') == 'A' ? 'selected' : '' }}>Grade A</option>
                                <option value="B" {{ request('grade') == 'B' ? 'selected' : '' }}>Grade B</option>
                                <option value="C" {{ request('grade') == 'C' ? 'selected' : '' }}>Grade C</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('tengkulak.dashboard') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nelayan</th>
                                <th>Jenis Ikan</th>
                                <th>Berat (Kg)</th>
                                <th>Grade</th>
                                <th>Harga/Kg</th>
                                <th>Total</th>
                                <th>Tanggal Tangkap</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stokTersedia as $index => $stok)
                            <tr>
                                <td>{{ $stokTersedia->firstItem() + $index }}</td>
                                <td>{{ $stok->nelayan->nama }}</td>
                                <td><strong>{{ $stok->jenisIkan->nama_ikan }}</strong></td>
                                <td>{{ number_format($stok->berat, 2) }}</td>
                                <td><span class="badge bg-info">{{ $stok->grade }}</span></td>
                                <td>Rp {{ number_format($stok->harga_per_kg, 0, ',', '.') }}</td>
                                <td><strong>Rp {{ number_format($stok->berat * $stok->harga_per_kg, 0, ',', '.') }}</strong></td>
                                <td>{{ $stok->tanggal_tangkap->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2">Tidak ada stok tersedia saat ini</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($stokTersedia->hasPages())
                <div class="mt-3">
                    {{ $stokTersedia->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Transactions -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-receipt"></i> Transaksi Terbaru
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Transaksi</th>
                                <th>Pembeli</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksi as $trx)
                            <tr>
                                <td><strong>{{ $trx->kode_transaksi }}</strong></td>
                                <td>{{ $trx->pembeli->nama }}</td>
                                <td>{{ $trx->tanggal_transaksi->format('d/m/Y') }}</td>
                                <td><strong>Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</strong></td>
                                <td>
                                    @if($trx->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($trx->status == 'dikemas')
                                        <span class="badge bg-info">Dikemas</span>
                                    @elseif($trx->status == 'dikirim')
                                        <span class="badge bg-primary">Dikirim</span>
                                    @elseif($trx->status == 'selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $trx->id }}">
                                        <i class="bi bi-eye"></i> Lihat
                                    </button>
                                </td>
                            </tr>

                            <!-- Detail Modal -->
                            <div class="modal fade" id="detailModal{{ $trx->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Transaksi {{ $trx->kode_transaksi }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h6>Informasi Transaksi</h6>
                                            <table class="table table-sm">
                                                <tr>
                                                    <td width="150">Kode</td>
                                                    <td><strong>{{ $trx->kode_transaksi }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Pembeli</td>
                                                    <td>{{ $trx->pembeli->nama }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tanggal</td>
                                                    <td>{{ $trx->tanggal_transaksi->format('d M Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Status</td>
                                                    <td>{{ ucfirst($trx->status) }}</td>
                                                </tr>
                                            </table>

                                            <h6 class="mt-3">Detail Item</h6>
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Ikan</th>
                                                        <th>Jumlah (Kg)</th>
                                                        <th>Harga/Kg</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($trx->details as $detail)
                                                    <tr>
                                                        <td>{{ $detail->hasilTangkapan->jenisIkan->nama_ikan }}</td>
                                                        <td>{{ number_format($detail->jumlah_kg, 2) }}</td>
                                                        <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                                    </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                                                        <td><strong>Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2">Belum ada transaksi</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Shipments -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-truck"></i> Status Pengiriman
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No. Resi</th>
                                <th>Kode Transaksi</th>
                                <th>Sopir</th>
                                <th>Tujuan</th>
                                <th>Tgl Kirim</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengiriman as $kirim)
                            <tr>
                                <td><strong>{{ $kirim->nomor_resi }}</strong></td>
                                <td>{{ $kirim->transaksi->kode_transaksi }}</td>
                                <td>{{ $kirim->sopir->nama }}</td>
                                <td>{{ Str::limit($kirim->alamat_tujuan, 30) }}</td>
                                <td>{{ $kirim->tanggal_kirim->format('d/m/Y') }}</td>
                                <td>
                                    @if($kirim->status == 'menunggu')
                                        <span class="badge bg-secondary">Menunggu</span>
                                    @elseif($kirim->status == 'dalam_perjalanan')
                                        <span class="badge bg-primary">Dalam Perjalanan</span>
                                    @elseif($kirim->status == 'terkirim')
                                        <span class="badge bg-success">Terkirim</span>
                                    @else
                                        <span class="badge bg-danger">Gagal</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2">Belum ada pengiriman</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
