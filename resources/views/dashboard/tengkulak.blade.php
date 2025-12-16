@extends('layouts.app')

@section('title', 'Dashboard Tengkulak')

@section('content')
<style>
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .stat-card.pending {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .stat-card.approved {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stat-card.transaksi {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    
    .stat-label {
        font-size: 0.95rem;
        opacity: 0.9;
    }

    .penawaran-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        transition: all 0.3s;
        margin-bottom: 15px;
        border-left: 4px solid #667eea;
    }

    .penawaran-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }

    .penawaran-header {
        padding: 15px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .penawaran-code {
        font-weight: 700;
        color: #667eea;
        font-size: 1rem;
    }

    .penawaran-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-approved {
        background: #d4edda;
        color: #155724;
    }

    .penawaran-body {
        padding: 15px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 15px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 3px;
    }

    .info-value {
        font-weight: 600;
        color: #212529;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e9ecef;
    }

    .action-buttons .btn {
        flex: 1;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        background: #f8f9fa;
        border-radius: 12px;
        color: #6c757d;
    }

    .empty-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2"><i class="bi bi-shop"></i> Dashboard Tengkulak</h2>
                    <p class="text-muted mb-0">Kelola penawaran ikan dari nelayan & transaksi pembeli</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card pending">
                <div class="stat-icon"><i class="bi bi-hourglass-split" style="font-size: 1.5rem;"></i></div>
                <div class="stat-number">{{ count($penawaranPending) }}</div>
                <div class="stat-label">Penawaran Menunggu</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card approved">
                <div class="stat-icon"><i class="bi bi-check-circle" style="font-size: 1.5rem;"></i></div>
                <div class="stat-number">{{ $penawaranApproved->total() }}</div>
                <div class="stat-label">Penawaran Approved</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card transaksi">
                <div class="stat-icon"><i class="bi bi-bag-check" style="font-size: 1.5rem;"></i></div>
                <div class="stat-number">{{ count($transaksi) }}</div>
                <div class="stat-label">Transaksi Pembeli</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-truck" style="font-size: 1.5rem;"></i></div>
                <div class="stat-number">{{ count($pengiriman) }}</div>
                <div class="stat-label">Pengiriman</div>
            </div>
        </div>
    </div>

    <!-- Penawaran Pending Approval -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-hourglass-split"></i> Penawaran Menunggu Persetujuan</h5>
                </div>
                <div class="card-body">
                    @if(count($penawaranPending) > 0)
                        <div class="row">
                            @foreach($penawaranPending as $penawaran)
                            <div class="col-md-6">
                                <div class="penawaran-card">
                                    <div class="penawaran-header">
                                        <div>
                                            <div class="penawaran-code">{{ $penawaran->kode_penawaran }}</div>
                                            <small class="text-muted">{{ $penawaran->nelayan->nama }}</small>
                                        </div>
                                        <span class="penawaran-status status-pending">PENDING</span>
                                    </div>
                                    <div class="penawaran-body">
                                        <div class="info-grid">
                                            <div class="info-item">
                                                <div class="info-label">Jenis Ikan</div>
                                                <div class="info-value">{{ $penawaran->jenisIkan->nama_ikan ?? 'N/A' }}</div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-label">Jumlah</div>
                                                <div class="info-value">{{ number_format($penawaran->jumlah_kg, 2) }} kg</div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-label">Harga/Kg</div>
                                                <div class="info-value">Rp {{ number_format($penawaran->harga_per_kg, 0, ',', '.') }}</div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-label">Kualitas</div>
                                                <div class="info-value">{{ $penawaran->kualitas }}</div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-label">Total Harga</div>
                                                <div class="info-value">Rp {{ number_format($penawaran->jumlah_kg * $penawaran->harga_per_kg, 0, ',', '.') }}</div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-label">Tanggal</div>
                                                <div class="info-value">{{ $penawaran->tanggal_tangkapan->format('d M Y') }}</div>
                                            </div>
                                        </div>
                                        <div class="action-buttons">
                                            <a href="{{ route('tengkulak.detail-penawaran-approval', $penawaran->id) }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye"></i> Lihat Detail & Approve
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                            <p>✓ Tidak ada penawaran menunggu persetujuan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Penawaran Approved -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-check-circle"></i> Penawaran Sudah Disetujui (Siap Dijual ke Pembeli)</h5>
                </div>
                <div class="card-body">
                    @if($penawaranApproved->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nelayan</th>
                                        <th>Jenis Ikan</th>
                                        <th>Stok (kg)</th>
                                        <th>Harga/Kg</th>
                                        <th>Total Value</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($penawaranApproved as $penawaran)
                                    <tr>
                                        <td><strong>{{ $penawaran->kode_penawaran }}</strong></td>
                                        <td>{{ $penawaran->nelayan->nama }}</td>
                                        <td>{{ $penawaran->jenisIkan->nama_ikan }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ number_format($penawaran->jumlah_kg, 2) }} kg</span>
                                        </td>
                                        <td>Rp {{ number_format($penawaran->harga_per_kg, 0, ',', '.') }}</td>
                                        <td><strong>Rp {{ number_format($penawaran->jumlah_kg * $penawaran->harga_per_kg, 0, ',', '.') }}</strong></td>
                                        <td><span class="badge bg-success">Approved</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($penawaranApproved->hasPages())
                        <div class="mt-3">
                            {{ $penawaranApproved->links() }}
                        </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-check-circle"></i></div>
                            <p>Tidak ada penawaran yang sudah disetujui (belum ada yang approved)</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Pembeli -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-bag-check"></i> Transaksi Pembeli (Pesanan Masuk)</h5>
                </div>
                <div class="card-body">
                    @if(count($transaksi) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode Transaksi</th>
                                        <th>Pembeli</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transaksi as $trx)
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
                                                <span class="badge bg-danger">{{ ucfirst($trx->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-bag"></i></div>
                            <p>Tidak ada transaksi dari pembeli (belum ada yang order)</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Pengiriman -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-truck"></i> Pengiriman</h5>
                </div>
                <div class="card-body">
                    @if(count($pengiriman) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nomor Resi</th>
                                        <th>Kode Transaksi</th>
                                        <th>Sopir</th>
                                        <th>Tgl Kirim</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pengiriman as $kirim)
                                    <tr>
                                        <td><strong>{{ $kirim->nomor_resi ?? 'N/A' }}</strong></td>
                                        <td>{{ $kirim->transaksi->kode_transaksi ?? 'N/A' }}</td>
                                        <td>{{ $kirim->sopir->nama ?? 'N/A' }}</td>
                                        <td>{{ $kirim->tanggal_kirim ? $kirim->tanggal_kirim->format('d/m/Y') : 'N/A' }}</td>
                                        <td>
                                            @if($kirim->status == 'menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($kirim->status == 'dalam_perjalanan')
                                                <span class="badge bg-info">Dalam Perjalanan</span>
                                            @elseif($kirim->status == 'terkirim')
                                                <span class="badge bg-success">Terkirim</span>
                                            @else
                                                <span class="badge bg-danger">{{ ucfirst($kirim->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-truck"></i></div>
                            <p>Tidak ada pengiriman</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
