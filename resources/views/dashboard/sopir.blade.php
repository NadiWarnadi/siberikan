@extends('layouts.app')

@section('title', 'Dashboard Sopir')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h2><i class="bi bi-truck"></i> Dashboard Sopir</h2>
            <p class="text-muted mb-0">Kelola pengiriman dan update status pengiriman</p>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('sopir.dashboard') }}">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari no resi atau alamat..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="dalam_perjalanan" {{ request('status') == 'dalam_perjalanan' ? 'selected' : '' }}>Dalam Perjalanan</option>
                                <option value="terkirim" {{ request('status') == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                                <option value="gagal" {{ request('status') == 'gagal' ? 'selected' : '' }}>Gagal</option>
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

<!-- Delivery List -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-task"></i> Daftar Pengiriman
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>No. Resi</th>
                                <th>Kode Transaksi</th>
                                <th>Pembeli</th>
                                <th>Alamat Tujuan</th>
                                <th>Tgl Kirim</th>
                                <th>Estimasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengiriman as $index => $kirim)
                            <tr>
                                <td>{{ $pengiriman->firstItem() + $index }}</td>
                                <td><strong>{{ $kirim->nomor_resi }}</strong></td>
                                <td>{{ $kirim->transaksi->kode_transaksi }}</td>
                                <td>{{ $kirim->transaksi->pembeli->nama }}</td>
                                <td>{{ Str::limit($kirim->alamat_tujuan, 40) }}</td>
                                <td>{{ $kirim->tanggal_kirim->format('d/m/Y') }}</td>
                                <td>{{ $kirim->tanggal_estimasi->format('d/m/Y') }}</td>
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
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $kirim->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($kirim->status != 'terkirim')
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#updateModal{{ $kirim->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @endif
                                    @if($kirim->status == 'dalam_perjalanan' && !$kirim->buktiSerahTerima)
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" 
                                            data-bs-target="#buktiModal{{ $kirim->id }}">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>

                            <!-- Detail Modal -->
                            <div class="modal fade" id="detailModal{{ $kirim->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Pengiriman</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td width="150">No. Resi</td>
                                                    <td><strong>{{ $kirim->nomor_resi }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Transaksi</td>
                                                    <td>{{ $kirim->transaksi->kode_transaksi }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Pembeli</td>
                                                    <td>{{ $kirim->transaksi->pembeli->nama }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tengkulak</td>
                                                    <td>{{ $kirim->transaksi->tengkulak->nama }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Alamat Tujuan</td>
                                                    <td>{{ $kirim->alamat_tujuan }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tgl Kirim</td>
                                                    <td>{{ $kirim->tanggal_kirim->format('d M Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Estimasi</td>
                                                    <td>{{ $kirim->tanggal_estimasi->format('d M Y') }}</td>
                                                </tr>
                                                @if($kirim->tanggal_diterima)
                                                <tr>
                                                    <td>Diterima</td>
                                                    <td>{{ $kirim->tanggal_diterima->format('d M Y') }}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td>Status</td>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $kirim->status)) }}</td>
                                                </tr>
                                                @if($kirim->catatan)
                                                <tr>
                                                    <td>Catatan</td>
                                                    <td>{{ $kirim->catatan }}</td>
                                                </tr>
                                                @endif
                                            </table>

                                            @if($kirim->buktiSerahTerima)
                                            <hr>
                                            <h6>Bukti Serah Terima</h6>
                                            <table class="table table-sm">
                                                <tr>
                                                    <td width="150">Penerima</td>
                                                    <td>{{ $kirim->buktiSerahTerima->nama_penerima }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Waktu</td>
                                                    <td>{{ $kirim->buktiSerahTerima->waktu_terima->format('d M Y H:i') }}</td>
                                                </tr>
                                                @if($kirim->buktiSerahTerima->catatan)
                                                <tr>
                                                    <td>Catatan</td>
                                                    <td>{{ $kirim->buktiSerahTerima->catatan }}</td>
                                                </tr>
                                                @endif
                                            </table>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Update Status Modal -->
                            <div class="modal fade" id="updateModal{{ $kirim->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update Status Pengiriman</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('sopir.pengiriman.status', $kirim->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="menunggu" {{ $kirim->status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                                        <option value="dalam_perjalanan" {{ $kirim->status == 'dalam_perjalanan' ? 'selected' : '' }}>Dalam Perjalanan</option>
                                                        <option value="terkirim" {{ $kirim->status == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                                                        <option value="gagal" {{ $kirim->status == 'gagal' ? 'selected' : '' }}>Gagal</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan</label>
                                                    <textarea name="catatan" class="form-control" rows="3">{{ $kirim->catatan }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Bukti Serah Terima Modal -->
                            <div class="modal fade" id="buktiModal{{ $kirim->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Input Bukti Serah Terima</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('sopir.pengiriman.bukti', $kirim->id) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="alert alert-info">
                                                    <i class="bi bi-info-circle"></i> Pastikan barang sudah diterima oleh pembeli
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Penerima</label>
                                                    <input type="text" name="nama_penerima" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan</label>
                                                    <textarea name="catatan" class="form-control" rows="3" 
                                                              placeholder="Kondisi barang, dll"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">Konfirmasi Terkirim</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2">Belum ada pengiriman yang ditugaskan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($pengiriman->hasPages())
            <div class="card-footer">
                {{ $pengiriman->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
