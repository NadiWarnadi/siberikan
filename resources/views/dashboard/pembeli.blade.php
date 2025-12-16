@extends('layouts.app')

@section('title', 'Dashboard Pembeli')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-bag-check"></i> Dashboard Pembeli</h2>
                <p class="text-muted mb-0">Kelola riwayat pembelian dan konfirmasi penerimaan barang</p>
            </div>
            <a href="{{ route('pembeli.browse') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-shop"></i> Jelajahi & Pesan Ikan
            </a>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('pembeli.dashboard') }}">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari kode transaksi..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="dikemas" {{ request('status') == 'dikemas' ? 'selected' : '' }}>Dikemas</option>
                                <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Transaction History -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> Riwayat Pembelian
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Transaksi</th>
                                <th>Tengkulak</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status Transaksi</th>
                                <th>Status Pengiriman</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksi as $index => $trx)
                            <tr>
                                <td>{{ $transaksi->firstItem() + $index }}</td>
                                <td><strong>{{ $trx->kode_transaksi }}</strong></td>
                                <td>{{ $trx->tengkulak->nama }}</td>
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
                                    @if($trx->pengiriman)
                                        @if($trx->pengiriman->status == 'menunggu')
                                            <span class="badge bg-secondary">Menunggu</span>
                                        @elseif($trx->pengiriman->status == 'dalam_perjalanan')
                                            <span class="badge bg-primary">Dalam Perjalanan</span>
                                        @elseif($trx->pengiriman->status == 'terkirim')
                                            <span class="badge bg-success">Terkirim</span>
                                        @else
                                            <span class="badge bg-danger">Gagal</span>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-dark">Belum Dikirim</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $trx->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($trx->pengiriman && $trx->pengiriman->status == 'dalam_perjalanan' && $trx->status != 'selesai')
                                    <form method="POST" action="{{ route('pembeli.konfirmasi', $trx->pengiriman->id) }}" 
                                          class="d-inline" onsubmit="return confirm('Konfirmasi bahwa barang sudah diterima?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-check-circle"></i> Terima
                                        </button>
                                    </form>
                                    @endif
                                    @if($trx->status == 'selesai')
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#returModal{{ $trx->id }}">
                                        <i class="bi bi-arrow-return-left"></i>
                                    </button>
                                    @endif
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
                                                    <td>Tengkulak</td>
                                                    <td>{{ $trx->tengkulak->nama }}</td>
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

                                            @if($trx->pengiriman)
                                            <h6 class="mt-3">Informasi Pengiriman</h6>
                                            <table class="table table-sm">
                                                <tr>
                                                    <td width="150">No. Resi</td>
                                                    <td>{{ $trx->pengiriman->nomor_resi }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Sopir</td>
                                                    <td>{{ $trx->pengiriman->sopir->nama }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Alamat</td>
                                                    <td>{{ $trx->pengiriman->alamat_tujuan }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Status</td>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $trx->pengiriman->status)) }}</td>
                                                </tr>
                                            </table>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Retur Modal -->
                            <div class="modal fade" id="returModal{{ $trx->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Ajukan Retur Barang</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('pembeli.retur') }}">
                                            @csrf
                                            <input type="hidden" name="transaksi_id" value="{{ $trx->id }}">
                                            <div class="modal-body">
                                                <div class="alert alert-warning">
                                                    <i class="bi bi-exclamation-triangle"></i> Pastikan alasan retur Anda valid
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Alasan Retur</label>
                                                    <select name="alasan" class="form-select" required>
                                                        <option value="">-- Pilih Alasan --</option>
                                                        <option value="rusak">Barang Rusak</option>
                                                        <option value="tidak_sesuai">Tidak Sesuai Pesanan</option>
                                                        <option value="kadaluarsa">Kadaluarsa</option>
                                                        <option value="lainnya">Lainnya</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Keterangan Detail</label>
                                                    <textarea name="keterangan" class="form-control" rows="3" required></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Jumlah Pengembalian (Rp)</label>
                                                    <input type="number" name="jumlah_pengembalian" class="form-control" 
                                                           max="{{ $trx->total_harga }}" step="0.01" required>
                                                    <small class="text-muted">Maksimal: Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning">Ajukan Retur</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2">Belum ada riwayat pembelian</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($transaksi->hasPages())
            <div class="card-footer">
                {{ $transaksi->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Return History -->
@if($retur->count() > 0)
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-arrow-return-left"></i> Riwayat Retur
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Transaksi</th>
                                <th>Tanggal Retur</th>
                                <th>Alasan</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($retur as $ret)
                            <tr>
                                <td>{{ $ret->transaksi->kode_transaksi }}</td>
                                <td>{{ $ret->tanggal_retur->format('d/m/Y') }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $ret->alasan)) }}</td>
                                <td>Rp {{ number_format($ret->jumlah_pengembalian, 0, ',', '.') }}</td>
                                <td>
                                    @if($ret->status == 'diajukan')
                                        <span class="badge bg-warning">Diajukan</span>
                                    @elseif($ret->status == 'disetujui')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($ret->status == 'ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-info">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
